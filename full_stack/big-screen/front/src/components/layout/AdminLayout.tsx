import React from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { 
  LayoutDashboard, 
  ClipboardList, 
  FileText, 
  LogOut, 
  User,
  Home
} from 'lucide-react';
import { useAuth } from '../../contexts/AuthContext';
import { toast } from 'react-hot-toast';

interface AdminLayoutProps {
  children: React.ReactNode;
}

export default function AdminLayout({ children }: AdminLayoutProps) {
  const location = useLocation();
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    try {
      await logout();
      toast.success('Déconnecté avec succès');
      navigate('/admin/login');
    } catch (error) {
      toast.error('Erreur lors de la déconnexion');
    }
  };

  const navigation = [
    {
      name: 'Tableau de bord',
      href: '/admin',
      icon: LayoutDashboard,
      current: location.pathname === '/admin'
    },
    {
      name: 'Questions',
      href: '/admin/questions',
      icon: ClipboardList,
      current: location.pathname === '/admin/questions'
    },
    {
      name: 'Réponses',
      href: '/admin/responses',
      icon: FileText,
      current: location.pathname === '/admin/responses'
    }
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Sidebar */}
      <div className="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div className="flex flex-col h-full">
          {/* Logo */}
          <div className="flex items-center h-16 px-6 border-b border-gray-200">
            <div className="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <ClipboardList className="w-5 h-5 text-white" />
            </div>
            <span className="ml-3 text-lg font-semibold text-gray-900">Admin Panel</span>
          </div>

          {/* Navigation */}
          <nav className="flex-1 px-4 py-6 space-y-2">
            {navigation.map((item) => {
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  to={item.href}
                  className={`flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors ${
                    item.current
                      ? 'bg-primary-100 text-primary-600'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                  }`}
                >
                  <Icon className="w-5 h-5 mr-3" />
                  {item.name}
                </Link>
              );
            })}
          </nav>

          {/* User Menu */}
          <div className="border-t border-gray-200 p-4">
            <div className="flex items-center mb-4">
              <div className="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                <User className="w-4 h-4 text-gray-600" />
              </div>
              <div className="ml-3">
                <p className="text-sm font-medium text-gray-900">{user?.name}</p>
                <p className="text-xs text-gray-500">{user?.email}</p>
              </div>
            </div>
            
            <div className="space-y-2">
              <Link
                to="/"
                className="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
              >
                <Home className="w-4 h-4 mr-3" />
                Site public
              </Link>
              
              <button
                onClick={handleLogout}
                className="flex items-center w-full px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
              >
                <LogOut className="w-4 h-4 mr-3" />
                Déconnexion
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="pl-64">
        <main className="p-8">
          {children}
        </main>
      </div>
    </div>
  );
}