import { NextApiRequest, NextApiResponse } from 'next';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'GET') {
    return res.status(405).json({ message: 'Method not allowed' });
  }

  try {
    const authHeader = req.headers.authorization;
    
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({
        success: false,
        message: 'Token tidak valid'
      });
    }

    const token = authHeader.substring(7);
    
    // Simple token validation (in production, validate JWT properly)
    if (token.startsWith('mock-jwt-token-')) {
      // Mock user data
      const user = {
        id: 1,
        name: 'Administrator',
        email: 'admin@afms.com',
        role: 'admin'
      };

      return res.status(200).json({
        success: true,
        message: 'Profile berhasil diambil',
        user
      });
    } else {
      return res.status(401).json({
        success: false,
        message: 'Token tidak valid'
      });
    }
  } catch (error) {
    console.error('Profile error:', error);
    return res.status(500).json({
      success: false,
      message: 'Terjadi kesalahan server'
    });
  }
}
