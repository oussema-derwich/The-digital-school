import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { toast } from 'react-hot-toast';
import { ChevronLeft, ChevronRight, Send, Loader2 } from 'lucide-react';
import axios from 'axios';

interface Question {
  id: number;
  question_number: number;
  title: string;
  content: string;
  type: 'A' | 'B' | 'C';
  options?: string[];
}

interface SurveyFormData {
  email: string;
  [key: string]: string;
}

export default function SurveyPage() {
  const [questions, setQuestions] = useState<Question[]>([]);
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const navigate = useNavigate();

  const { register, handleSubmit, formState: { errors }, watch, setValue } = useForm<SurveyFormData>();

  useEffect(() => {
    loadQuestions();
  }, []);

  const loadQuestions = async () => {
    try {
      const response = await axios.get('/api/questions');
      setQuestions(response.data);
    } catch (error) {
      toast.error('Erreur lors du chargement des questions');
    } finally {
      setLoading(false);
    }
  };

  const onSubmit = async (data: SurveyFormData) => {
    setSubmitting(true);
    try {
      const answers: { [key: number]: string } = {};
      questions.forEach(question => {
        answers[question.question_number] = data[`question_${question.question_number}`];
      });

      const response = await axios.post('/api/survey', {
        email: data.email,
        answers
      });

      toast.success(response.data.message);
      navigate('/thank-you', { 
        state: { 
          token: response.data.token,
          responseUrl: response.data.response_url 
        } 
      });
    } catch (error: any) {
      toast.error(error.response?.data?.error || 'Erreur lors de la soumission');
    } finally {
      setSubmitting(false);
    }
  };

  const nextQuestion = () => {
    if (currentQuestion < questions.length) {
      setCurrentQuestion(prev => prev + 1);
    }
  };

  const prevQuestion = () => {
    if (currentQuestion > 0) {
      setCurrentQuestion(prev => prev - 1);
    }
  };

  const renderQuestion = (question: Question) => {
    const fieldName = `question_${question.question_number}`;
    
    switch (question.type) {
      case 'A': // Choix multiple
        return (
          <div className="space-y-3">
            {question.options?.map((option, index) => (
              <label key={index} className="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                <input
                  type="radio"
                  value={option}
                  {...register(fieldName, { required: 'Cette question est obligatoire' })}
                  className="text-primary-600 focus:ring-primary-500"
                />
                <span className="text-gray-700">{option}</span>
              </label>
            ))}
          </div>
        );

      case 'B': // Texte libre
        return (
          <textarea
            {...register(fieldName, { 
              required: 'Cette question est obligatoire',
              maxLength: { value: 255, message: 'Maximum 255 caractères' }
            })}
            className="input min-h-24 resize-none"
            placeholder="Votre réponse..."
            maxLength={255}
          />
        );

      case 'C': // Échelle 1-5
        return (
          <div className="flex justify-between items-center space-x-2">
            <span className="text-sm text-gray-500 min-w-max">Pas du tout</span>
            <div className="flex space-x-4">
              {[1, 2, 3, 4, 5].map(value => (
                <label key={value} className="flex flex-col items-center space-y-2 cursor-pointer">
                  <input
                    type="radio"
                    value={value.toString()}
                    {...register(fieldName, { required: 'Cette question est obligatoire' })}
                    className="text-primary-600 focus:ring-primary-500"
                  />
                  <span className="text-sm font-medium text-gray-700">{value}</span>
                </label>
              ))}
            </div>
            <span className="text-sm text-gray-500 min-w-max">Tout à fait</span>
          </div>
        );

      default:
        return null;
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50 flex items-center justify-center">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  const isEmailStep = currentQuestion === 0;
  const isQuestionStep = currentQuestion > 0 && currentQuestion <= questions.length;
  const isReviewStep = currentQuestion === questions.length + 1;

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50">
      <div className="max-w-4xl mx-auto px-4 py-8">
        {/* Progress Bar */}
        <div className="mb-8">
          <div className="flex justify-between text-sm text-gray-600 mb-2">
            <span>Progression</span>
            <span>{Math.round((currentQuestion / (questions.length + 1)) * 100)}%</span>
          </div>
          <div className="w-full bg-gray-200 rounded-full h-2">
            <div 
              className="bg-primary-600 h-2 rounded-full transition-all duration-300"
              style={{ width: `${(currentQuestion / (questions.length + 1)) * 100}%` }}
            ></div>
          </div>
        </div>

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-8">
          <div className="card min-h-96">
            {/* Email Step */}
            {isEmailStep && (
              <div className="animate-fade-in">
                <h2 className="text-2xl font-bold text-gray-900 mb-6">
                  Bienvenue dans notre sondage
                </h2>
                <p className="text-gray-600 mb-8">
                  Votre participation nous aide à améliorer nos services. Le sondage prend environ 5 minutes.
                </p>
                
                <div>
                  <label className="label">Votre adresse email *</label>
                  <input
                    type="email"
                    {...register('email', { 
                      required: 'L\'adresse email est obligatoire',
                      pattern: {
                        value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                        message: 'Adresse email invalide'
                      }
                    })}
                    className="input"
                    placeholder="votre@email.com"
                  />
                  {errors.email && (
                    <p className="text-error-500 text-sm mt-1">{errors.email.message}</p>
                  )}
                </div>
              </div>
            )}

            {/* Question Steps */}
            {isQuestionStep && (
              <div className="animate-fade-in">
                {(() => {
                  const question = questions[currentQuestion - 1];
                  if (!question) return null;
                  
                  return (
                    <>
                      <div className="mb-6">
                        <div className="flex justify-between items-center mb-4">
                          <span className="text-sm font-medium text-primary-600">
                            Question {question.question_number} sur {questions.length}
                          </span>
                          <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            Type {question.type}
                          </span>
                        </div>
                        <h3 className="text-xl font-semibold text-gray-900 mb-2">
                          {question.title}
                        </h3>
                        <p className="text-gray-600">
                          {question.content}
                        </p>
                      </div>

                      <div>
                        {renderQuestion(question)}
                        {errors[`question_${question.question_number}`] && (
                          <p className="text-error-500 text-sm mt-2">
                            {errors[`question_${question.question_number}`]?.message}
                          </p>
                        )}
                      </div>
                    </>
                  );
                })()}
              </div>
            )}

            {/* Review Step */}
            {isReviewStep && (
              <div className="animate-fade-in">
                <h3 className="text-xl font-semibold text-gray-900 mb-6">
                  Récapitulatif de vos réponses
                </h3>
                <div className="bg-gray-50 rounded-lg p-6 mb-6 max-h-96 overflow-y-auto">
                  <div className="mb-4">
                    <strong>Email :</strong> {watch('email')}
                  </div>
                  {questions.map(question => {
                    const answer = watch(`question_${question.question_number}`);
                    return (
                      <div key={question.id} className="mb-3 pb-3 border-b border-gray-200">
                        <strong>Q{question.question_number}. {question.title} :</strong>
                        <div className="text-gray-700 mt-1">{answer || 'Non répondu'}</div>
                      </div>
                    );
                  })}
                </div>
                <p className="text-sm text-gray-600">
                  Vérifiez vos réponses avant de soumettre le formulaire.
                </p>
              </div>
            )}
          </div>

          {/* Navigation Buttons */}
          <div className="flex justify-between">
            <button
              type="button"
              onClick={prevQuestion}
              disabled={currentQuestion === 0}
              className="btn btn-secondary disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft className="w-4 h-4 mr-2" />
              Précédent
            </button>

            {currentQuestion < questions.length + 1 ? (
              <button
                type="button"
                onClick={nextQuestion}
                className="btn btn-primary"
              >
                Suivant
                <ChevronRight className="w-4 h-4 ml-2" />
              </button>
            ) : (
              <button
                type="submit"
                disabled={submitting}
                className="btn btn-success disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {submitting ? (
                  <>
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    Envoi en cours...
                  </>
                ) : (
                  <>
                    <Send className="w-4 h-4 mr-2" />
                    Envoyer le sondage
                  </>
                )}
              </button>
            )}
          </div>
        </form>
      </div>
    </div>
  );
}