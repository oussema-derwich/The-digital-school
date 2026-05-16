import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { CheckCircle, ExternalLink, Home } from 'lucide-react';

export default function ThankYouPage() {
  const location = useLocation();
  const { token, responseUrl } = location.state || {};

  return (
    <div className="min-h-screen bg-gradient-to-br from-success-50 via-white to-primary-50 flex items-center justify-center px-4">
      <div className="max-w-2xl mx-auto text-center">
        <div className="card">
          <div className="animate-bounce mb-6">
            <CheckCircle className="w-20 h-20 text-success-600 mx-auto" />
          </div>
          
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            Merci pour votre participation !
          </h1>
          
          <p className="text-lg text-gray-600 mb-8">
            Vos réponses ont été enregistrées avec succès. Votre contribution nous aide à améliorer nos services.
          </p>

          {token && responseUrl && (
            <div className="bg-primary-50 border border-primary-200 rounded-lg p-6 mb-8">
              <h3 className="text-lg font-semibold text-primary-900 mb-3">
                Consultez vos réponses
              </h3>
              <p className="text-primary-700 mb-4">
                Vous pouvez consulter vos réponses à tout moment grâce à ce lien sécurisé :
              </p>
              <div className="flex flex-col sm:flex-row gap-4 items-center justify-center">
                <code className="bg-white border border-primary-300 rounded px-3 py-2 text-sm text-primary-800 font-mono break-all">
                  {window.location.origin}{responseUrl}
                </code>
                <Link
                  to={responseUrl}
                  className="btn btn-primary"
                >
                  <ExternalLink className="w-4 h-4 mr-2" />
                  Voir mes réponses
                </Link>
              </div>
            </div>
          )}

          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link to="/" className="btn btn-secondary">
              <Home className="w-4 h-4 mr-2" />
              Retour à l'accueil
            </Link>
            <Link to="/survey" className="btn btn-primary">
              Nouveau sondage
            </Link>
          </div>

          <div className="mt-8 pt-6 border-t border-gray-200">
            <p className="text-sm text-gray-500">
              💡 <strong>Astuce :</strong> Sauvegardez le lien pour consulter vos réponses ultérieurement
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}