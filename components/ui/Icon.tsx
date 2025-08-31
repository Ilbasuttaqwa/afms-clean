import React from 'react';
import * as Heroicons from '@heroicons/react/24/outline';
import * as LucideIcons from 'lucide-react';

export type IconName = 
  // Heroicons
  | 'home' | 'user' | 'users' | 'cog' | 'chart-bar' | 'clock' | 'calendar'
  | 'document' | 'folder' | 'search' | 'plus' | 'minus' | 'x-mark'
  | 'check' | 'exclamation-triangle' | 'information-circle'
  | 'arrow-left' | 'arrow-right' | 'arrow-up' | 'arrow-down'
  | 'chevron-left' | 'chevron-right' | 'chevron-up' | 'chevron-down'
  | 'eye' | 'eye-slash' | 'lock-closed' | 'lock-open'
  | 'device-phone-mobile' | 'computer-desktop' | 'server'
  | 'finger-print' | 'identification' | 'credit-card'
  | 'building-office' | 'map-pin' | 'phone' | 'envelope'
  | 'calendar-days' | 'clock' | 'currency-dollar'
  | 'chart-pie' | 'presentation-chart-line' | 'table-cells'
  | 'clipboard-document-list' | 'document-text' | 'archive-box'
  | 'trash' | 'pencil' | 'pencil-square' | 'square-2-stack'
  | 'arrow-path' | 'arrow-trending-up' | 'arrow-trending-down'
  | 'signal' | 'wifi' | 'battery-full' | 'battery-half' | 'battery-empty'
  | 'sun' | 'moon' | 'adjustments-horizontal' | 'adjustments-vertical'
  | 'funnel' | 'magnifying-glass' | 'funnel'
  // Lucide Icons
  | 'activity' | 'airplay' | 'alert-circle' | 'alert-octagon' | 'alert-triangle'
  | 'align-center' | 'align-justify' | 'align-left' | 'align-right'
  | 'anchor' | 'aperture' | 'archive' | 'arrow-down-circle' | 'arrow-down-left'
  | 'arrow-down-right' | 'arrow-down' | 'arrow-left-circle' | 'arrow-left'
  | 'arrow-right-circle' | 'arrow-right' | 'arrow-up-circle' | 'arrow-up-left'
  | 'arrow-up-right' | 'arrow-up' | 'at-sign' | 'award' | 'bar-chart'
  | 'battery' | 'bell' | 'bluetooth' | 'bold' | 'book' | 'bookmark'
  | 'box' | 'briefcase' | 'calendar' | 'camera' | 'cast' | 'check-circle'
  | 'check-square' | 'check' | 'chevron-down' | 'chevron-left' | 'chevron-right'
  | 'chevron-up' | 'chrome' | 'circle' | 'clipboard' | 'clock' | 'cloud'
  | 'code' | 'codepen' | 'codesandbox' | 'coffee' | 'columns' | 'command'
  | 'compass' | 'copy' | 'corner-down-left' | 'corner-down-right'
  | 'corner-left-down' | 'corner-left-up' | 'corner-right-down' | 'corner-right-up'
  | 'corner-up-left' | 'corner-up-right' | 'cpu' | 'credit-card' | 'crop'
  | 'crosshair' | 'database' | 'delete' | 'disc' | 'dollar-sign' | 'download'
  | 'droplet' | 'edit' | 'edit-2' | 'edit-3' | 'external-link' | 'eye'
  | 'eye-off' | 'facebook' | 'fast-forward' | 'feather' | 'figma' | 'file'
  | 'film' | 'filter' | 'flag' | 'folder' | 'github' | 'gitlab' | 'globe'
  | 'grid' | 'hard-drive' | 'hash' | 'headphones' | 'heart' | 'help-circle'
  | 'hexagon' | 'home' | 'image' | 'inbox' | 'info' | 'instagram' | 'italic'
  | 'key' | 'layers' | 'layout' | 'life-buoy' | 'link' | 'link-2' | 'linkedin'
  | 'list' | 'loader' | 'lock' | 'log-in' | 'log-out' | 'mail' | 'map-pin'
  | 'maximize' | 'maximize-2' | 'menu' | 'message-circle' | 'message-square'
  | 'mic' | 'mic-off' | 'minimize' | 'minimize-2' | 'monitor' | 'moon'
  | 'more-horizontal' | 'more-vertical' | 'move' | 'music' | 'navigation'
  | 'navigation-2' | 'octagon' | 'package' | 'paperclip' | 'pause-circle'
  | 'pause' | 'percent' | 'phone' | 'phone-call' | 'phone-forwarded'
  | 'phone-incoming' | 'phone-missed' | 'phone-off' | 'phone-outgoing'
  | 'pie-chart' | 'play-circle' | 'play' | 'plus-circle' | 'plus-square'
  | 'plus' | 'pocket' | 'power' | 'printer' | 'radio' | 'refresh-ccw'
  | 'refresh-cw' | 'repeat' | 'rewind' | 'rotate-ccw' | 'rotate-cw'
  | 'rss' | 'save' | 'scissors' | 'search' | 'send' | 'server' | 'settings'
  | 'share' | 'share-2' | 'shield' | 'shield-off' | 'shuffle' | 'sidebar'
  | 'skip-back' | 'skip-forward' | 'slack' | 'slash' | 'sliders' | 'smartphone'
  | 'speaker' | 'square' | 'star' | 'stop-circle' | 'sun' | 'sunrise'
  | 'sunset' | 'tablet' | 'tag' | 'target' | 'terminal' | 'thermometer'
  | 'thumbs-down' | 'thumbs-up' | 'toggle-left' | 'toggle-right' | 'tool'
  | 'trash' | 'trash-2' | 'trending-down' | 'trending-up' | 'triangle'
  | 'truck' | 'tv' | 'twitch' | 'twitter' | 'type' | 'umbrella' | 'underline'
  | 'unlock' | 'upload' | 'user' | 'user-check' | 'user-minus' | 'user-plus'
  | 'user-x' | 'users' | 'video' | 'video-off' | 'voicemail' | 'volume'
  | 'volume-1' | 'volume-2' | 'volume-x' | 'watch' | 'wifi' | 'wifi-off'
  | 'wind' | 'x-circle' | 'x-octagon' | 'x-square' | 'x' | 'youtube' | 'zap'
  | 'zap-off' | 'zoom-in' | 'zoom-out';

interface IconProps {
  name: IconName;
  size?: number;
  className?: string;
  strokeWidth?: number;
}

export const Icon: React.FC<IconProps> = ({ 
  name, 
  size = 24, 
  className = '', 
  strokeWidth = 2 
}) => {
  // Try Heroicons first
  const HeroiconComponent = Heroicons[name as keyof typeof Heroicons];
  if (HeroiconComponent) {
    return (
      <HeroiconComponent 
        width={size} 
        height={size} 
        className={className}
        strokeWidth={strokeWidth}
      />
    );
  }

  // Try Lucide icons
  const LucideComponent = LucideIcons[name as keyof typeof LucideIcons];
  if (LucideComponent) {
    return (
      <LucideComponent 
        size={size} 
        className={className}
        strokeWidth={strokeWidth}
      />
    );
  }

  // Fallback to a default icon
  return (
    <div 
      className={`w-${size} h-${size} bg-gray-300 rounded ${className}`}
      style={{ width: size, height: size }}
    />
  );
};

export default Icon;
