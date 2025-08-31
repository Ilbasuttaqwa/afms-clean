import { NextApiRequest, NextApiResponse } from 'next';

export interface AuthenticatedRequest extends NextApiRequest {
  user?: {
    id: number;
    username: string;
    role: string;
    nama_pegawai?: string;
  };
}

export function requireRole(allowedRoles: string[]) {
  return function(handler: (req: AuthenticatedRequest, res: NextApiResponse) => Promise<void> | void) {
    return async (req: AuthenticatedRequest, res: NextApiResponse) => {
      try {
        // Check if user is authenticated
        if (!req.user) {
          return res.status(401).json({
            success: false,
            message: 'Unauthorized - User not authenticated'
          });
        }

        // Check if user has required role
        if (!allowedRoles.includes(req.user.role)) {
          return res.status(403).json({
            success: false,
            message: 'Forbidden - Insufficient permissions'
          });
        }

        // User is authorized, proceed with handler
        return handler(req, res);
      } catch (error) {
        console.error('Auth middleware error:', error);
        return res.status(500).json({
          success: false,
          message: 'Internal server error'
        });
      }
    };
  };
}

export function requireAuth(handler: (req: AuthenticatedRequest, res: NextApiResponse) => Promise<void> | void) {
  return async (req: AuthenticatedRequest, res: NextApiResponse) => {
    try {
      if (!req.user) {
        return res.status(401).json({
          success: false,
          message: 'Unauthorized - User not authenticated'
        });
      }

      return handler(req, res);
    } catch (error) {
      console.error('Auth middleware error:', error);
      return res.status(500).json({
        success: false,
        message: 'Internal server error'
      });
    }
  };
}
