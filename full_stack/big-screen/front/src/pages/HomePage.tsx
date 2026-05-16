import React from 'react';
import { Link } from 'react-router-dom';
import { ClipboardList, BarChart3, Users, Shield } from 'lucide-react';

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50">
      {/* Header */}
      <header className="bg-white/80 backdrop-blur-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-6">
            <div className="flex items-center space-x-3">
              <div className="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                <ClipboardList className="w-6 h-6 text-white" />
              </div>
              <h1 className="text-2xl font-bold text-gray-900">BigScreen Survey</h1>
            </div>
            <Link
              to="/admin/login"
              className="flex items-center space-x-2 text-gray-600 hover:text-primary-600 transition-colors"
            >
              <Shield className="w-4 h-4" />
              <span className="text-sm font-medium">Administration</span>
            </Link>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="text-center">
          <h2 className="text-4xl font-bold text-gray-900 sm:text-6xl mb-6">
            Votre avis nous intéresse
          </h2>
          <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto text-balance">
            Participez à notre enquête et aidez-nous à améliorer nos services. 
            Vos réponses sont importantes et contribuent à façonner l'avenir de nos produits.
          </p>
          
          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <Link
              to="/survey"
              className="btn btn-primary px-8 py-4 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all"
            >
              Commencer le sondage
            </Link>
            <button className="btn btn-secondary px-8 py-4 text-lg font-semibold rounded-xl">
              En savoir plus
            </button>
          </div>

          {/* Features */}
          <div className="grid md:grid-cols-3 gap-8 mt-20">
            <div className="card text-center hover:shadow-md transition-shadow">
              <div className="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                <ClipboardList className="w-6 h-6 text-primary-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Questionnaire simple
              </h3>
              <p className="text-gray-600">
                20 questions rapides et faciles à répondre pour partager votre expérience.
              </p>
            </div>
            
            <div className="card text-center hover:shadow-md transition-shadow">
              <div className="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                <BarChart3 className="w-6 h-6 text-secondary-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Résultats transparents
              </h3>
              <p className="text-gray-600">
                Consultez vos réponses à tout moment grâce à votre lien personnel sécurisé.
              </p>
            </div>
            
            <div className="card text-center hover:shadow-md transition-shadow">
              <div className="w-12 h-12 bg-accent-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                <Users className="w-6 h-6 text-accent-600" />
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Impact collectif
              </h3>
              <p className="text-gray-600">
                Vos réponses contribuent à améliorer l'expérience pour tous nos utilisateurs.
              </p>
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 mt-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="text-center">
            <p className="text-gray-600">
              © 2024 BigScreen Survey. Développé dans le cadre d'un projet éducatif.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}