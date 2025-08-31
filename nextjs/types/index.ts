export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  error?: string;
}

export interface User {
  id: number;
  username: string;
  email: string;
  role: string;
  nama_pegawai?: string;
  jabatan?: {
    id: number;
    nama_jabatan: string;
  };
  cabang?: {
    id: number;
    nama_cabang: string;
  };
  created_at: string;
  updated_at: string;
}

export interface Employee {
  id: number;
  nama_pegawai: string;
  nip: string;
  email: string;
  no_telp: string;
  alamat: string;
  tanggal_lahir: string;
  jenis_kelamin: 'L' | 'P';
  status_pernikahan: 'belum_menikah' | 'menikah' | 'cerai';
  tanggal_bergabung: string;
  jabatan_id: number;
  cabang_id: number;
  user_id?: number;
  created_at: string;
  updated_at: string;
}

export interface Branch {
  id: number;
  nama_cabang: string;
  alamat: string;
  no_telp: string;
  email: string;
  created_at: string;
  updated_at: string;
}

export interface Position {
  id: number;
  nama_jabatan: string;
  deskripsi?: string;
  gaji_pokok: number;
  tunjangan: number;
  created_at: string;
  updated_at: string;
}

export interface Attendance {
  id: number;
  karyawan_id: number;
  tanggal: string;
  jam_masuk?: string;
  jam_keluar?: string;
  status: 'hadir' | 'terlambat' | 'alpha' | 'izin' | 'sakit';
  keterangan?: string;
  created_at: string;
  updated_at: string;
}

export interface FingerprintDevice {
  id: number;
  nama_device: string;
  ip_address: string;
  port: number;
  status: 'active' | 'inactive' | 'maintenance';
  cabang_id: number;
  created_at: string;
  updated_at: string;
}

export interface Payroll {
  id: number;
  karyawan_id: number;
  bulan: number;
  tahun: number;
  gaji_pokok: number;
  tunjangan: number;
  potongan: number;
  total_gaji: number;
  status: 'pending' | 'approved' | 'paid';
  created_at: string;
  updated_at: string;
}
