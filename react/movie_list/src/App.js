import React from 'react';
import { MovieList } from './movieList';
import moviesData from './movieData';
import './movies.css';


function App() {
  return (
    <div>
      <MovieList movies={moviesData} />
    </div>
  );
}

export default App;

