import React, { useState, useEffect } from 'react';
import { ClipboardList, Hash, Type, List } from 'lucide-react';
import axios from 'axios';

interface Question {
  id: number;
  question_number: number;
  title: string;
  content: string;
  type: 'A' | 'B' | 'C';
  options?: string[];
}

export default function AdminQuestions() {
  const [questions, setQuestions] = useState<Question[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadQuestions();
  }, []);

  const loadQuestions = async () => {
    try {
      const response = await axios.get('/api/admin/questions');
      setQuestions(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des questions:', error);
    } finally {
      setLoading(false);
    }
  };

  const getTypeLabel = (type: string) => {
    switch (type) {
      case 'A':
        return 'Choix multiple';
      case 'B':
        return 'Texte libre';
      case 'C':
        return 'Échelle 1-5';
      default:
        return type;
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'A':
        return 'bg-primary-100 text-primary-600';
      case 'B':
        return 'bg-secondary-100 text-secondary-600';
      case 'C':
        return 'bg-accent-100 text-accent-600';
      default:
        return 'bg-gray-100 text-gray-600';
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
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <ClipboardList className="w-8 h-8 mr-3 text-primary-600" />
            Questionnaire
          </h1>
          <p className="text-gray-600 mt-2">Gestion des questions du sondage</p>
        </div>
        
        <div className="bg-white border border-gray-200 rounded-lg px-4 py-2">
          <span className="text-sm text-gray-600">Total : </span>
          <span className="text-lg font-bold text-primary-600">{questions.length} questions</span>
        </div>
      </div>

      {/* Questions Table */}
      <div className="card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <div className="flex items-center">
                    <Hash className="w-4 h-4 mr-2" />
                    Numéro
                  </div>
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Intitulé
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <div className="flex items-center">
                    <Type className="w-4 h-4 mr-2" />
                    Type
                  </div>
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contenu
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {questions.map((question) => (
                <tr key={question.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center justify-center w-8 h-8 bg-primary-100 text-primary-600 text-sm font-semibold rounded-full">
                      {question.question_number}
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <div className="text-sm font-medium text-gray-900">
                      {question.title}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getTypeColor(question.type)}`}>
                      {question.type} - {getTypeLabel(question.type)}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <div className="text-sm text-gray-900 max-w-md">
                      <p className="line-clamp-2">{question.content}</p>
                      {question.type === 'A' && question.options && (
                        <div className="mt-2">
                          <div className="flex items-center text-xs text-gray-500 mb-1">
                            <List className="w-3 h-3 mr-1" />
                            Options :
                          </div>
                          <div className="flex flex-wrap gap-1">
                            {question.options.map((option, index) => (
                              <span
                                key={index}
                                className="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded"
                              >
                                {option}
                              </span>
                            ))}
                          </div>
                        </div>
                      )}
                      {question.type === 'C' && (
                        <div className="mt-2">
                          <div className="flex items-center text-xs text-gray-500">
                            <span>Échelle : 1 (Pas du tout) → 5 (Tout à fait)</span>
                          </div>
                        </div>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Question Types Legend */}
      <div className="card">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Types de questions</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="flex items-center p-4 bg-primary-50 rounded-lg">
            <div className="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
              A
            </div>
            <div>
              <h4 className="font-medium text-primary-900">Choix multiple</h4>
              <p className="text-sm text-primary-700">Sélection parmi plusieurs options</p>
            </div>
          </div>
          
          <div className="flex items-center p-4 bg-secondary-50 rounded-lg">
            <div className="w-8 h-8 bg-secondary-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
              B
            </div>
            <div>
              <h4 className="font-medium text-secondary-900">Texte libre</h4>
              <p className="text-sm text-secondary-700">Réponse textuelle (max. 255 caractères)</p>
            </div>
          </div>
          
          <div className="flex items-center p-4 bg-accent-50 rounded-lg">
            <div className="w-8 h-8 bg-accent-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">
              C
            </div>
            <div>
              <h4 className="font-medium text-accent-900">Échelle</h4>
              <p className="text-sm text-accent-700">Notation de 1 à 5</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}