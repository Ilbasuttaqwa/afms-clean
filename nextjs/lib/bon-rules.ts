export interface EmployeeData {
  id: number;
  nama_pegawai: string;
  gaji_pokok: number;
  tunjangan: number;
  tanggal_bergabung: string;
  status_pernikahan: 'belum_menikah' | 'menikah' | 'cerai';
  jumlah_anak: number;
}

export interface ExistingBon {
  id: number;
  jumlah: number;
  sisa_cicilan: number;
  status: 'active' | 'completed' | 'defaulted';
}

export interface BonRules {
  max_bon_percentage: number;
  min_tenure_months: number;
  max_installment_periods: number;
  interest_rate: number;
  max_concurrent_bons: number;
}

export const defaultBonRules: BonRules = {
  max_bon_percentage: 0.3, // 30% dari gaji
  min_tenure_months: 6, // Minimal 6 bulan kerja
  max_installment_periods: 24, // Maksimal 24 bulan cicilan
  interest_rate: 0.05, // 5% per tahun
  max_concurrent_bons: 1 // Maksimal 1 bon aktif
};

export function calculateMaxBonAmount(employee: EmployeeData, rules: BonRules = defaultBonRules): number {
  const totalIncome = employee.gaji_pokok + employee.tunjangan;
  return Math.floor(totalIncome * rules.max_bon_percentage);
}

export function calculateRecommendedInstallmentPeriod(
  bonAmount: number, 
  employee: EmployeeData, 
  rules: BonRules = defaultBonRules
): number {
  const monthlyIncome = employee.gaji_pokok + employee.tunjangan;
  const maxMonthlyPayment = monthlyIncome * 0.4; // Maksimal 40% dari gaji bulanan
  
  if (maxMonthlyPayment <= 0) return 1;
  
  const recommendedPeriod = Math.ceil(bonAmount / maxMonthlyPayment);
  return Math.min(recommendedPeriod, rules.max_installment_periods);
}

export function getBonEligibilityStatus(
  employee: EmployeeData, 
  existingBons: ExistingBon[], 
  rules: BonRules = defaultBonRules
): { eligible: boolean; reason?: string; maxAmount: number } {
  // Check tenure
  const joinDate = new Date(employee.tanggal_bergabung);
  const currentDate = new Date();
  const tenureMonths = (currentDate.getFullYear() - joinDate.getFullYear()) * 12 + 
                      (currentDate.getMonth() - joinDate.getMonth());
  
  if (tenureMonths < rules.min_tenure_months) {
    return {
      eligible: false,
      reason: `Minimal masa kerja ${rules.min_tenure_months} bulan`,
      maxAmount: 0
    };
  }
  
  // Check concurrent bons
  const activeBons = existingBons.filter(bon => bon.status === 'active');
  if (activeBons.length >= rules.max_concurrent_bons) {
    return {
      eligible: false,
      reason: `Sudah memiliki ${activeBons.length} bon aktif`,
      maxAmount: 0
    };
  }
  
  // Check total outstanding debt
  const totalOutstanding = activeBons.reduce((sum, bon) => sum + bon.sisa_cicilan, 0);
  const maxTotalDebt = (employee.gaji_pokok + employee.tunjangan) * 0.5; // Maksimal 50% dari gaji
  
  if (totalOutstanding >= maxTotalDebt) {
    return {
      eligible: false,
      reason: 'Total hutang sudah melebihi batas maksimal',
      maxAmount: 0
    };
  }
  
  const maxAmount = calculateMaxBonAmount(employee, rules);
  const availableAmount = maxAmount - totalOutstanding;
  
  if (availableAmount <= 0) {
    return {
      eligible: false,
      reason: 'Tidak ada sisa kuota bon yang tersedia',
      maxAmount: 0
    };
  }
  
  return {
    eligible: true,
    maxAmount: availableAmount
  };
}

export function calculateMonthlyInstallment(
  bonAmount: number, 
  period: number, 
  rules: BonRules = defaultBonRules
): number {
  const monthlyInterest = rules.interest_rate / 12;
  const totalInterest = bonAmount * monthlyInterest * period;
  const totalAmount = bonAmount + totalInterest;
  
  return Math.ceil(totalAmount / period);
}

export function calculateTotalInterest(
  bonAmount: number, 
  period: number, 
  rules: BonRules = defaultBonRules
): number {
  const monthlyInterest = rules.interest_rate / 12;
  return bonAmount * monthlyInterest * period;
}
