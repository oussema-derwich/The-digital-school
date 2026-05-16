import React, { useState, useEffect } from 'react';
import { FileText, Search, Calendar, Filter, Download } from 'lucide-react';
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

export default function AdminResponses() {
  const [responses, setResponses] = useState<SurveyResponse[]>([]);
  const [questions, setQuestions] = useState<Question[]>([]);
  const [selectedResponse, setSelectedResponse] = useState<SurveyResponse | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [responsesResponse, questionsResponse] = await Promise.all([
        axios.get('/api/admin/responses'),
        axios.get('/api/admin/questions')
      ]);
      
      setResponses(responsesResponse.data);
      setQuestions(questionsResponse.data);
    } catch (error) {
      console.error('Erreur lors du chargement des données:', error);
    } finally {
      setLoading(false);
    }
  };

  const filteredResponses = responses.filter(response =>
    response.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('fr-FR', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const exportToCSV = () => {
    if (responses.length === 0) return;

    const headers = ['Email', 'Date', ...questions.map(q => `Q${q.question_number} - ${q.title}`)];
    const csvContent = [
      headers.join(','),
      ...responses.map(response => [
        response.email,
        formatDate(response.created_at),
        ...questions.map(q => response.answers[q.question_number] || '')
      ].map(field => `"${field}"`).join(','))
    ].join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `reponses_sondage_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <FileText className="w-8 h-8 mr-3 text-primary-600" />
            Réponses
          </h1>
          <p className="text-gray-600 mt-2">Consultation des réponses des participants</p>
        </div>
        
        <button
          onClick={exportToCSV}
          className="btn btn-secondary"
        >
          <Download className="w-4 h-4 mr-2" />
          Exporter CSV
        </button>
      </div>

      {/* Search and Filters */}
      <div className="card">
        <div className="flex flex-col sm:flex-row gap-4">
          <div className="flex-1">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Rechercher par email..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="input pl-10"
              />
            </div>
          </div>
          <div className="flex items-center gap-2 text-sm text-gray-600">
            <Filter className="w-4 h-4" />
            {filteredResponses.length} / {responses.length} réponses
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Responses List */}
        <div className="card">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">
            Liste des participants
          </h3>
          <div className="space-y-3 max-h-96 overflow-y-auto">
            {filteredResponses.map((response) => (
              <div
                key={response.id}
                onClick={() => setSelectedResponse(response)}
                className={`p-4 border border-gray-200 rounded-lg cursor-pointer transition-all ${
                  selectedResponse?.id === response.id
                    ? 'border-primary-500 bg-primary-50'
                    : 'hover:border-gray-300 hover:bg-gray-50'
                }`}
              >
                <div className="flex items-center justify-between">
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 truncate">
                      {response.email}
                    </p>
                    <div className="flex items-center text-xs text-gray-500 mt-1">
                      <Calendar className="w-3 h-3 mr-1" />
                      {formatDate(response.created_at)}
                    </div>
                  </div>
                  <div className="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                    {Object.keys(response.answers).length} réponses
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Response Details */}
        <div className="card">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">
            Détail des réponses
          </h3>
          
          {selectedResponse ? (
            <div className="space-y-4">
              <div className="bg-gray-50 rounded-lg p-4 mb-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="font-medium text-gray-900">{selectedResponse.email}</p>
                    <p className="text-sm text-gray-500">{formatDate(selectedResponse.created_at)}</p>
                  </div>
                </div>
              </div>
              
              <div className="max-h-64 overflow-y-auto space-y-3">
                {questions.map((question) => {
                  const answer = selectedResponse.answers[question.question_number];
                  return (
                    <div key={question.id} className="border-b border-gray-100 pb-3">
                      <div className="flex items-start gap-3">
                        <span className="flex-shrink-0 w-6 h-6 bg-primary-100 text-primary-600 text-xs font-semibold rounded-full flex items-center justify-center">
                          {question.question_number}
                        </span>
                        <div className="flex-1 min-w-0">
                          <p className="text-sm font-medium text-gray-900">{question.title}</p>
                          <p className="text-sm text-gray-700 mt-1">
                            {answer ? (
                              question.type === 'C' ? (
                                <span className="flex items-center gap-2">
                                  <span className="font-semibold text-primary-600">{answer}/5</span>
                                  <div className="flex">
                                    {[1, 2, 3, 4, 5].map(star => (
                                      <span
                                        key={star}
                                        className={`text-sm ${
                                          star <= parseInt(answer) ? 'text-yellow-400' : 'text-gray-300'
                                        }`}
                                      >
                                        ★
                                      </span>
                                    ))}
                                  </div>
                                </span>
                              ) : (
                                <span className={question.type === 'B' ? 'italic' : ''}>{answer}</span>
                              )
                            ) : (
                              <span className="text-gray-400 italic">Non répondu</span>
                            )}
                          </p>
                        </div>
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          ) : (
            <div className="text-center py-12 text-gray-500">
              <FileText className="w-12 h-12 mx-auto mb-4 text-gray-300" />
              <p>Sélectionnez un participant pour voir ses réponses</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}