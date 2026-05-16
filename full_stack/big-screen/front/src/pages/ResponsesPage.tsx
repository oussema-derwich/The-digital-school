import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { FileText, Calendar, Mail, Home, AlertCircle } from 'lucide-react';
import axios from 'axios';

interface Question {
  id: number;
  question_number: number;
  title: string;
  content: string;
  type: 'A' | 'B' | 'C';
  options?: string[];
}

interface SurveyResponse {
  id: number;
  email: string;
  token: string;
  answers: { [key: number]: string };
  created_at: string;
}

export default function ResponsesPage() {
  const { token } = useParams<{ token: string }>();
  const [response, setResponse] = useState<SurveyResponse | null>(null);
  const [questions, setQuestions] = useState<Question[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (token) {
      loadResponse();
    }
  }, [token]);

  const loadResponse = async () => {
    try {
      const result = await axios.get(`/api/responses/${token}`);
      setResponse(result.data.response);
      setQuestions(result.data.questions);
    } catch (error: any) {
      setError(error.response?.data?.error || 'Erreur lors du chargement');
    } finally {
      setLoading(false);
    }
  };

  const getQuestionAnswer = (questionNumber: number) => {
    return response?.answers[questionNumber] || 'Non répondu';
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('fr-FR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  if (error || !response) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50 flex items-center justify-center px-4">
        <div className="max-w-md mx-auto text-center">
          <div className="card">
            <AlertCircle className="w-16 h-16 text-error-500 mx-auto mb-4" />
            <h1 className="text-2xl font-bold text-gray-900 mb-4">
              Réponse non trouvée
            </h1>
            <p className="text-gray-600 mb-6">
              Le lien que vous avez utilisé est invalide ou a expiré.
            </p>
            <Link to="/" className="btn btn-primary">
              <Home className="w-4 h-4 mr-2" />
              Retour à l'accueil
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50">
      <div className="max-w-4xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="card mb-8">
          <div className="flex items-start justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-900 mb-4 flex items-center">
                <FileText className="w-8 h-8 mr-3 text-primary-600" />
                Vos réponses au sondage
              </h1>
              <div className="flex flex-col sm:flex-row sm:items-center gap-4 text-gray-600">
                <div className="flex items-center">
                  <Mail className="w-4 h-4 mr-2" />
                  {response.email}
                </div>
                <div className="flex items-center">
                  <Calendar className="w-4 h-4 mr-2" />
                  {formatDate(response.created_at)}
                </div>
              </div>
            </div>
            <Link to="/" className="btn btn-secondary">
              <Home className="w-4 h-4 mr-2" />
              Accueil
            </Link>
          </div>
        </div>

        {/* Responses */}
        <div className="space-y-6">
          {questions.map((question) => (
            <div key={question.id} className="card hover:shadow-md transition-shadow">
              <div className="flex flex-col sm:flex-row sm:items-start gap-4">
                <div className="flex-shrink-0">
                  <span className="inline-flex items-center justify-center w-8 h-8 bg-primary-100 text-primary-600 text-sm font-semibold rounded-full">
                    {question.question_number}
                  </span>
                </div>
                
                <div className="flex-1">
                  <div className="flex items-center gap-2 mb-2">
                    <h3 className="text-lg font-semibold text-gray-900">
                      {question.title}
                    </h3>
                    <span className="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                      Type {question.type}
                    </span>
                  </div>
                  
                  <p className="text-gray-600 mb-3">
                    {question.content}
                  </p>
                  
                  <div className="bg-gray-50 rounded-lg p-4">
                    <h4 className="text-sm font-medium text-gray-700 mb-2">Votre réponse :</h4>
                    <div className="text-gray-900">
                      {question.type === 'C' ? (
                        <div className="flex items-center gap-2">
                          <span className="font-semibold text-primary-600">
                            {getQuestionAnswer(question.question_number)}/5
                          </span>
                          <div className="flex">
                            {[1, 2, 3, 4, 5].map(star => (
                              <div
                                key={star}
                                className={`w-5 h-5 ${
                                  star <= parseInt(getQuestionAnswer(question.question_number))
                                    ? 'text-yellow-400'
                                    : 'text-gray-300'
                                }`}
                              >
                                ★
                              </div>
                            ))}
                          </div>
                        </div>
                      ) : (
                        <p className={`${question.type === 'B' ? 'italic' : ''}`}>
                          {getQuestionAnswer(question.question_number)}
                        </p>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Footer */}
        <div className="card mt-8 text-center">
          <p className="text-gray-600 mb-4">
            Merci d'avoir participé à notre sondage !
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link to="/survey" className="btn btn-primary">
              Répondre à nouveau
            </Link>
            <Link to="/" className="btn btn-secondary">
              Retour à l'accueil
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}