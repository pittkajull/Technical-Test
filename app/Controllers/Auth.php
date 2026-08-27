<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ApplicationLogModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show login page
     */
    public function index()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - Sistem Pemesanan Kendaraan'
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($username) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password harus diisi');
        }

        // Find user by username
        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan');
        }

        // Check if user is active
        if (!$user->is_active) {
            return redirect()->back()->withInput()->with('error', 'Akun tidak aktif');
        }

        // Verify password
        if (!password_verify($password, $user->password)) {
            return redirect()->back()->withInput()->with('error', 'Password salah');
        }

        // Set session data
        $sessionData = [
            'user_id' => $user->id,
            'username' => $user->username,
            'fullname' => $user->fullname,
            'role' => $user->role,
            'is_logged_in' => true
        ];

        session()->set($sessionData);

        // Update last login
        $this->userModel->updateLastLogin($user->id);

        // Log the login
        $this->logModel->log(
            $user->id,
            'LOGIN',
            'User ' . $user->username . ' berhasil login'
        );

        return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user->fullname);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $userId = session()->get('user_id');
        $username = session()->get('username');

        // Log the logout
        if ($userId) {
            $this->logModel->log(
                $userId,
                'LOGOUT',
                'User ' . $username . ' berhasil logout'
            );
        }

        // Destroy session
        session()->destroy();

        return redirect()->to('/auth')->with('success', 'Anda telah berhasil logout');
    }
}
