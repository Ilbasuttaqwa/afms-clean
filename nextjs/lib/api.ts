// API functions for AFMS
export const deviceApi = {
  getDevices: async () => {
    try {
      const response = await fetch('/api/devices', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch devices');
    }
  },

  createDevice: async (deviceData: any) => {
    try {
      const response = await fetch('/api/devices', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(deviceData),
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to create device');
    }
  },

  updateDevice: async (id: string, deviceData: any) => {
    try {
      const response = await fetch(`/api/devices/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(deviceData),
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to update device');
    }
  },

  deleteDevice: async (id: string) => {
    try {
      const response = await fetch(`/api/devices/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to delete device');
    }
  },
};

export const employeeApi = {
  getEmployees: async () => {
    try {
      const response = await fetch('/api/employees', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch employees');
    }
  },
};

export const attendanceApi = {
  getAttendances: async () => {
    try {
      const response = await fetch('/api/attendances', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch attendances');
    }
  },
};

// Additional APIs
export const dashboardApi = {
  getStats: async () => {
    try {
      const response = await fetch('/api/dasbor/stats', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch dashboard stats');
    }
  },

  getRecentActivities: async () => {
    try {
      const response = await fetch('/api/dasbor/activities', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch recent activities');
    }
  },
};

export const absensiApi = {
  getAbsensi: async () => {
    try {
      const response = await fetch('/api/absensi', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch absensi');
    }
  },
};

export const karyawanApi = {
  getKaryawan: async () => {
    try {
      const response = await fetch('/api/karyawan', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch karyawan');
    }
  },
};

export const cabangApi = {
  getCabang: async () => {
    try {
      const response = await fetch('/api/cabang', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch cabang');
    }
  },
};

export const jabatanApi = {
  getJabatan: async () => {
    try {
      const response = await fetch('/api/jabatan', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch jabatan');
    }
  },
};

export const payrollApi = {
  getPayroll: async () => {
    try {
      const response = await fetch('/api/payroll', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch payroll');
    }
  },
};

export const monitoringApi = {
  getDevices: async () => {
    try {
      const response = await fetch('/api/pemantauan/devices', {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      return await response.json();
    } catch (error) {
      throw new Error('Failed to fetch monitoring devices');
    }
  },
};