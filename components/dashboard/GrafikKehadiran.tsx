import React from 'react';

interface AttendanceData {
  date: string;
  present: number;
  absent: number;
  late: number;
}

interface GrafikKehadiranProps {
  data: AttendanceData[];
}

const GrafikKehadiran: React.FC<GrafikKehadiranProps> = ({ data }) => {
  if (!data || data.length === 0) {
    return (
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Grafik Kehadiran</h3>
        <div className="text-center py-8">
          <p className="text-gray-500">Belum ada data kehadiran</p>
        </div>
      </div>
    );
  }

  const maxValue = Math.max(...data.map(d => Math.max(d.present, d.absent, d.late)));

  return (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">Grafik Kehadiran (7 Hari Terakhir)</h3>
      <div className="space-y-4">
        {data.map((item, index) => (
          <div key={index} className="space-y-2">
            <div className="flex justify-between text-sm">
              <span className="text-gray-600">{item.date}</span>
              <span className="text-gray-900 font-medium">
                Total: {item.present + item.absent + item.late}
              </span>
            </div>
            <div className="flex space-x-1 h-6">
              {/* Present Bar */}
              <div 
                className="bg-green-500 rounded-l"
                style={{ 
                  width: `${(item.present / maxValue) * 100}%`,
                  minWidth: item.present > 0 ? '4px' : '0'
                }}
                title={`Hadir: ${item.present}`}
              />
              {/* Late Bar */}
              <div 
                className="bg-yellow-500"
                style={{ 
                  width: `${(item.late / maxValue) * 100}%`,
                  minWidth: item.late > 0 ? '4px' : '0'
                }}
                title={`Terlambat: ${item.late}`}
              />
              {/* Absent Bar */}
              <div 
                className="bg-red-500 rounded-r"
                style={{ 
                  width: `${(item.absent / maxValue) * 100}%`,
                  minWidth: item.absent > 0 ? '4px' : '0'
                }}
                title={`Tidak Hadir: ${item.absent}`}
              />
            </div>
            <div className="flex justify-between text-xs text-gray-500">
              <span>Hadir: {item.present}</span>
              <span>Terlambat: {item.late}</span>
              <span>Tidak Hadir: {item.absent}</span>
            </div>
          </div>
        ))}
      </div>
      <div className="mt-4 flex justify-center space-x-4 text-xs">
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 bg-green-500 rounded"></div>
          <span>Hadir</span>
        </div>
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 bg-yellow-500 rounded"></div>
          <span>Terlambat</span>
        </div>
        <div className="flex items-center space-x-1">
          <div className="w-3 h-3 bg-red-500 rounded"></div>
          <span>Tidak Hadir</span>
        </div>
      </div>
    </div>
  );
};

export default GrafikKehadiran;
