import { useRouter } from 'next/router';
import { useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';

export function withManagerAuth<P extends object>(
  WrappedComponent: React.ComponentType<P>
) {
  return function WithManagerAuthComponent(props: P) {
    const { user, isAuthenticated } = useAuth();
    const router = useRouter();

    useEffect(() => {
      if (!isAuthenticated) {
        router.push('/login');
        return;
      }

      if (user && !['admin', 'manager'].includes(user.role)) {
        router.push('/dasbor');
        return;
      }
    }, [isAuthenticated, user, router]);

    if (!isAuthenticated || !user || !['admin', 'manager'].includes(user.role)) {
      return null;
    }

    return <WrappedComponent {...props} />;
  };
}

export function withAdminOrManagerAuth<P extends object>(
  WrappedComponent: React.ComponentType<P>
) {
  return function WithAdminOrManagerAuthComponent(props: P) {
    const { user, isAuthenticated } = useAuth();
    const router = useRouter();

    useEffect(() => {
      if (!isAuthenticated) {
        router.push('/login');
        return;
      }

      if (user && !['admin', 'manager'].includes(user.role)) {
        router.push('/dasbor');
        return;
      }
    }, [isAuthenticated, user, router]);

    if (!isAuthenticated || !user || !['admin', 'manager'].includes(user.role)) {
      return null;
    }

    return <WrappedComponent {...props} />;
  };
}
