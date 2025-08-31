import { useEffect } from 'react'
import { useRouter } from 'next/router'
import { motion } from 'framer-motion'
import { useAuth } from '../contexts/AuthContext'
import { Icon } from '../components/ui/Icon'
import { Button } from '../components/ui/Button'

export default function Dashboard() {
  const { user, isAuthenticated, isLoading, logout } = useAuth()
  const router = useRouter()

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      router.replace('/login')
    }
  }, [isAuthenticated, isLoading, router])

  const handleLogout = async () => {
    await logout()
    router.replace('/login')
  }

  if (isLoading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600"></div>
      </div>
    )
  }

  if (!isAuthenticated) {
    return null
  }

  const stats = [
    {
      title: 'Total Karyawan',
      value: '156',
      change: '+12%',
      changeType: 'positive',
      icon: 'users',
      color: 'blue'
    },
    {
      title: 'Kehadiran Hari Ini',
      value: '142',
      change: '+8%',
      changeType: 'positive',
      icon: 'check-circle',
      color: 'green'
    },
    {
      title: 'Device Aktif',
      value: '24',
      change: '+2',
      changeType: 'positive',
      icon: 'computer-desktop',
      color: 'purple'
    },
    {
      title: 'Total Absensi',
      value: '2,847',
      change: '+15%',
      changeType: 'positive',
      icon: 'chart-bar',
      color: 'orange'
    }
  ]

  const recentActivities = [
    {
      id: 1,
      employee: 'Ahmad Rizki',
      action: 'Check In',
      time: '08:00',
      device: 'Device A-01',
      status: 'success'
    },
    {
      id: 2,
      employee: 'Siti Nurhaliza',
      action: 'Check Out',
      time: '17:30',
      device: 'Device A-02',
      status: 'success'
    },
    {
      id: 3,
      employee: 'Budi Santoso',
      action: 'Check In',
      time: '08:15',
      device: 'Device B-01',
      status: 'success'
    },
    {
      id: 4,
      employee: 'Dewi Sartika',
      action: 'Check In',
      time: '08:05',
      device: 'Device A-01',
      status: 'success'
    }
  ]

  const getColorClasses = (color: string) => {
    const colors = {
      blue: 'bg-blue-500 text-blue-100',
      green: 'bg-green-500 text-green-100',
      purple: 'bg-purple-500 text-purple-100',
      orange: 'bg-orange-500 text-orange-100'
    }
    return colors[color as keyof typeof colors] || colors.blue
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-4">
            <div className="flex items-center space-x-4">
              <div className="h-10 w-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                <Icon name="finger-print" size={24} className="text-white" />
              </div>
              <h1 className="text-2xl font-bold text-gray-900">AFMS Dashboard</h1>
            </div>
            <div className="flex items-center space-x-4">
              <div className="text-right">
                <p className="text-sm font-medium text-gray-900">{user?.name}</p>
                <p className="text-xs text-gray-500">{user?.role}</p>
              </div>
              <Button
                onClick={handleLogout}
                variant="outline"
                size="sm"
                leftIcon={<Icon name="log-out" size={16} />}
              >
                Logout
              </Button>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Welcome Section */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          className="mb-8"
        >
          <h2 className="text-3xl font-bold text-gray-900 mb-2">
            Selamat Datang, {user?.name}! 👋
          </h2>
          <p className="text-gray-600">
            Berikut adalah ringkasan aktivitas sistem AFMS hari ini
          </p>
        </motion.div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <motion.div
              key={stat.title}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow"
            >
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                  <p className="text-3xl font-bold text-gray-900 mt-2">{stat.value}</p>
                  <div className="flex items-center mt-2">
                    <span className={`text-sm font-medium ${
                      stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                    }`}>
                      {stat.change}
                    </span>
                    <span className="text-sm text-gray-500 ml-1">dari kemarin</span>
                  </div>
                </div>
                <div className={`h-12 w-12 rounded-lg flex items-center justify-center ${getColorClasses(stat.color)}`}>
                  <Icon name={stat.icon as any} size={24} />
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        {/* Quick Actions */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.4 }}
          className="bg-white rounded-xl shadow-sm border p-6 mb-8"
        >
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Button
              variant="outline"
              className="h-20 flex flex-col items-center justify-center space-y-2"
              onClick={() => router.push('/admin/karyawan')}
            >
              <Icon name="user-plus" size={24} className="text-blue-600" />
              <span className="text-sm font-medium">Tambah Karyawan</span>
            </Button>
            <Button
              variant="outline"
              className="h-20 flex flex-col items-center justify-center space-y-2"
              onClick={() => router.push('/admin/devices')}
            >
              <Icon name="computer-desktop" size={24} className="text-green-600" />
              <span className="text-sm font-medium">Kelola Device</span>
            </Button>
            <Button
              variant="outline"
              className="h-20 flex flex-col items-center justify-center space-y-2"
              onClick={() => router.push('/admin/absensi')}
            >
              <Icon name="chart-bar" size={24} className="text-purple-600" />
              <span className="text-sm font-medium">Lihat Absensi</span>
            </Button>
          </div>
        </motion.div>

        {/* Recent Activities */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.5 }}
          className="bg-white rounded-xl shadow-sm border p-6"
        >
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
          <div className="space-y-3">
            {recentActivities.map((activity, index) => (
              <motion.div
                key={activity.id}
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.5, delay: 0.6 + index * 0.1 }}
                className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div className="flex items-center space-x-3">
                  <div className={`h-8 w-8 rounded-full flex items-center justify-center ${
                    activity.status === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
                  }`}>
                    <Icon 
                      name={activity.status === 'success' ? 'check-circle' : 'x-circle'} 
                      size={16} 
                    />
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-900">{activity.employee}</p>
                    <p className="text-xs text-gray-500">{activity.device}</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-sm font-medium text-gray-900">{activity.action}</p>
                  <p className="text-xs text-gray-500">{activity.time}</p>
                </div>
              </motion.div>
            ))}
          </div>
        </motion.div>
      </main>
    </div>
  )
}