import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider } from './contexts/AuthContext';

// Pages
import HomePage from './pages/HomePage';
import SurveyPage from './pages/SurveyPage';
import ThankYouPage from './pages/ThankYouPage';
import ResponsesPage from './pages/ResponsesPage';
import AdminLogin from './pages/admin/AdminLogin';
import AdminDashboard from './pages/admin/AdminDashboard';
import AdminQuestions from './pages/admin/AdminQuestions';
import AdminResponses from './pages/admin/AdminResponses';

// Layout
import AdminLayout from './components/layout/AdminLayout';
import ProtectedRoute from './components/auth/ProtectedRoute';

function App() {
  return (
    <AuthProvider>
      <Router future={{ v7_relativeSplatPath: true }}>
        <div className="min-h-screen bg-gray-50">
          <Routes>
            {/* Public routes */}
            <Route path="/" element={<HomePage />} />
            <Route path="/survey" element={<SurveyPage />} />
            <Route path="/thank-you" element={<ThankYouPage />} />
            <Route path="/responses/:token" element={<ResponsesPage />} />
            
            {/* Admin routes */}
            <Route path="/admin/login" element={<AdminLogin />} />
            <Route path="/admin" element={
              <ProtectedRoute>
                <AdminLayout>
                  <AdminDashboard />
                </AdminLayout>
              </ProtectedRoute>
            } />
            <Route path="/admin/questions" element={
              <ProtectedRoute>
                <AdminLayout>
                  <AdminQuestions />
                </AdminLayout>
              </ProtectedRoute>
            } />
            <Route path="/admin/responses" element={
              <ProtectedRoute>
                <AdminLayout>
                  <AdminResponses />
                </AdminLayout>
              </ProtectedRoute>
            } />
          </Routes>
          
          <Toaster 
            position="top-right"
            toastOptions={{
              duration: 4000,
              className: 'text-sm',
            }}
          />
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;