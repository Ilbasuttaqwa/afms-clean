import * as z from "zod"

export const loginSchema = z.object({
  email: z.string().email("Email tidak valid"),
  password: z.string().min(1, "Password harus diisi"),
})

export const employeeSchema = z.object({
  name: z.string().min(2, "Nama minimal 2 karakter"),
  email: z.string().email("Email tidak valid").optional().or(z.literal("")),
  phone: z.string().optional(),
  employee_id: z.string().min(1, "ID Karyawan harus diisi"),
  branch_id: z.number().optional(),
  position_id: z.number().optional(),
  department: z.string().optional(),
  hire_date: z.string().optional(),
  status: z.enum(["active", "inactive", "terminated"]).default("active"),
})

export const attendanceSchema = z.object({
  employee_id: z.number().min(1, "Karyawan harus dipilih"),
  device_id: z.number().min(1, "Device harus dipilih"),
  attendance_time: z.string().min(1, "Waktu kehadiran harus diisi"),
  attendance_type: z.enum(["0", "1"]).default("0"), // 0: check-in, 1: check-out
  verification_type: z.enum(["1", "2", "3"]).default("1"), // 1: fingerprint, 2: card, 3: manual
  work_code: z.string().optional(),
  notes: z.string().optional(),
})

export type LoginFormData = z.infer<typeof loginSchema>
export type EmployeeFormData = z.infer<typeof employeeSchema>
export type AttendanceFormData = z.infer<typeof attendanceSchema>
