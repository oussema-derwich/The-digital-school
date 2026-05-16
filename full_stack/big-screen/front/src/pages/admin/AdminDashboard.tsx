import React, { useState, useEffect } from 'react';
import { BarChart3, Users, ClipboardList, TrendingUp } from 'lucide-react';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
} from 'chart.js';
import { Pie, Radar } from 'react-chartjs-2';
import axios from 'axios';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler
);

interface Statistics {
  pie_charts: {
    [key: number]: {
      question: {
        title: string;
        content: string;
      };
      data: { [key: string]: number };
    };
  };
  radar_chart: Array<{
    question: string;
    average: number;
  }>;
}

export default function AdminDashboard() {
  const [statistics, setStatistics] = useState<Statistics | null>(null);
  const [responses, setResponses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [statsResponse, responsesResponse] = await Promise.all([
        axios.get('/api/admin/statistics'),
        axios.get('/api/admin/responses')
      ]);
      
      setStatistics(statsResponse.data);
      setResponses(responsesResponse.data);
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error);
    } finally {
      setLoading(false);
    }
  };

  const createPieChartData = (data: { [key: string]: number }, title: string) => {
    const colors = ['#3B82F6', '#14B8A6', '#F97316', '#EF4444', '#8B5CF6'];
    
    return {
      labels: Object.keys(data),
      datasets: [
        {
          label: title,
          data: Object.values(data),
          backgroundColor: colors.slice(0, Object.keys(data).length),
          borderColor: colors.slice(0, Object.keys(data).length).map(color => color),
          borderWidth: 2,
        },
      ],
    };
  };

  const createRadarChartData = () => {
    if (!statistics?.radar_chart) return null;

    return {
      labels: statistics.radar_chart.map(item => item.question),
      datasets: [
        {
          label: 'Moyenne des évaluations',
          data: statistics.radar_chart.map(item => item.average),
          backgroundColor: 'rgba(59, 130, 246, 0.2)',
          borderColor: 'rgb(59, 130, 246)',
          borderWidth: 2,
          pointBackgroundColor: 'rgb(59, 130, 246)',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: 'rgb(59, 130, 246)',
        },
      ],
    };
  };

  const chartOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom' as const,
      },
    },
  };

  const radarOptions = {
    responsive: true,
    plugins: {
      legend: {
        position: 'top' as const,
      },
    },
    scales: {
      r: {
        angleLines: {
          display: true
        },
        suggestedMin: 0,
        suggestedMax: 5
      }
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Tableau de bord</h1>
        <p className="text-gray-600 mt-2">Vue d'ensemble des réponses et statistiques</p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="card">
          <div className="flex items-center">
            <div className="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
              <Users className="w-6 h-6 text-primary-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-600">Total Réponses</p>
              <p className="text-2xl font-bold text-gray-900">{responses.length}</p>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="flex items-center">
            <div className="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
              <ClipboardList className="w-6 h-6 text-secondary-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-600">Questions</p>
              <p className="text-2xl font-bold text-gray-900">20</p>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="flex items-center">
            <div className="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center">
              <BarChart3 className="w-6 h-6 text-accent-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-600">Taux de réponse</p>
              <p className="text-2xl font-bold text-gray-900">100%</p>
            </div>
          </div>
        </div>

        <div className="card">
          <div className="flex items-center">
            <div className="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
              <TrendingUp className="w-6 h-6 text-success-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-600">Satisfaction</p>
              <p className="text-2xl font-bold text-gray-900">
                {statistics?.radar_chart ? 
                  (statistics.radar_chart.reduce((acc, item) => acc + item.average, 0) / statistics.radar_chart.length).toFixed(1) 
                  : 'N/A'}/5
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Pie Charts */}
      {statistics?.pie_charts && (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {Object.entries(statistics.pie_charts).map(([questionNumber, data]) => (
            <div key={questionNumber} className="card">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">
                Q{questionNumber}. {data.question.title}
              </h3>
              <div className="h-64">
                <Pie 
                  data={createPieChartData(data.data, data.question.title)} 
                  options={chartOptions}
                />
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Radar Chart */}
      {statistics?.radar_chart && (
        <div className="card">
          <h3 className="text-xl font-semibold text-gray-900 mb-6">
            Évaluation détaillée (Questions 11-15)
          </h3>
          <div className="h-96">
            <Radar 
              data={createRadarChartData()!} 
              options={radarOptions}
            />
          </div>
        </div>
      )}

      {/* Recent Responses */}
      <div className="card">
        <h3 className="text-xl font-semibold text-gray-900 mb-6">
          Réponses récentes
        </h3>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Réponses
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {responses.slice(0, 5).map((response) => (
                <tr key={response.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {response.email}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {new Date(response.created_at).toLocaleDateString('fr-FR')}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {Object.keys(response.answers).length} réponses
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}