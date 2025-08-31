import React from 'react';
import { Icon } from './Icon';

interface ToastProps {
  id: string;
  message: string;
  type: 'success' | 'error' | 'warning' | 'info';
  onRemove: (id: string) => void;
}

const Toast: React.FC<ToastProps> = ({ id, message, type, onRemove }) => {
  const getIconName = () => {
    switch (type) {
      case 'success': return 'check-circle';
      case 'error': return 'x-circle';
      case 'warning': return 'exclamation-triangle';
      case 'info': return 'information-circle';
      default: return 'information-circle';
    }
  };

  const getBgColor = () => {
    switch (type) {
      case 'success': return 'bg-green-50 border-green-200 text-green-800';
      case 'error': return 'bg-red-50 border-red-200 text-red-800';
      case 'warning': return 'bg-yellow-50 border-yellow-200 text-yellow-800';
      case 'info': return 'bg-blue-50 border-blue-200 text-blue-800';
      default: return 'bg-blue-50 border-blue-200 text-blue-800';
    }
  };

  const getIconColor = () => {
    switch (type) {
      case 'success': return 'text-green-400';
      case 'error': return 'text-red-400';
      case 'warning': return 'text-yellow-400';
      case 'info': return 'text-blue-400';
      default: return 'text-blue-400';
    }
  };

  return (
    <div className={`${getBgColor()} border rounded-lg p-4 shadow-lg max-w-sm w-full mb-3 transition-all duration-300 ease-in-out transform translate-x-0 opacity-100`}>
      <div className="flex items-start">
        <div className="flex-shrink-0">
          <Icon name={getIconName()} size={20} className={getIconColor()} />
        </div>
        <div className="ml-3 flex-1">
          <p className="text-sm font-medium">{message}</p>
        </div>
        <div className="ml-4 flex-shrink-0">
          <button
            onClick={() => onRemove(id)}
            className="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
          >
            <Icon name="x-mark" size={16} />
          </button>
        </div>
      </div>
    </div>
  );
};

export default Toast;
