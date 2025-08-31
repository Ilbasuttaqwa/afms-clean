import { NextApiRequest, NextApiResponse } from 'next';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method not allowed' });
  }

  try {
    const { email, password } = req.body;

    // Simple validation
    if (!email || !password) {
      return res.status(400).json({
        success: false,
        message: 'Email dan password harus diisi'
      });
    }

    // Mock authentication - replace with real authentication logic
    if (email === 'admin@afms.com' && password === 'admin123') {
      // Mock user data
      const user = {
        id: 1,
        name: 'Administrator',
        email: 'admin@afms.com',
        role: 'admin'
      };

      return res.status(200).json({
        success: true,
        message: 'Login berhasil',
        user,
        token: 'mock-jwt-token-' + Date.now()
      });
    } else {
      return res.status(401).json({
        success: false,
        message: 'Email atau password salah'
      });
    }
  } catch (error) {
    console.error('Login error:', error);
    return res.status(500).json({
      success: false,
      message: 'Terjadi kesalahan server'
    });
  }
}
