import React, { useReducer } from "react";
import PropTypes from "prop-types";
import styled from "styled-components";
import { FaBookmark, FaThumbsUp } from "react-icons/fa";

// Images
import docteurSleep from "./images/docteur-sleep.jpg";
import joker from "./images/joker.jpg";
import theKing from "./images/the-king.jpg";

/* =========================
   🎬 DATA (inline)
========================= */
const moviesData = [
  {
    id: "1",
    title: "Joker",
    image: joker,
    category: "Drama",
  },
  {
    id: "2",
    title: "The King",
    image: theKing,
    category: "History",
  },
  {
    id: "3",
    title: "Doctor Sleep",
    image: docteurSleep,
    category: "Horror",
  },
];

/* =========================
   🎨 STYLES (PRO)
========================= */

const Container = styled.div`
  min-height: 100vh;
  background: #141414;
  padding: 30px;
  font-family: "Poppins", sans-serif;
`;

const Title = styled.h1`
  color: #e50914;
  text-align: center;
  margin-bottom: 30px;
`;

const Grid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
`;

const MovieCard = styled.div`
  background: #1c1c1c;
  border-radius: 12px;
  overflow: hidden;
  transition: 0.3s;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);

  &:hover {
    transform: scale(1.05);
  }
`;

const MovieImage = styled.img`
  width: 100%;
  height: 320px;
  object-fit: cover;
`;

const MovieContent = styled.div`
  padding: 12px;
`;

const MovieTitle = styled.h2`
  font-size: 16px;
  color: white;
`;

const MovieCategory = styled.p`
  font-size: 12px;
  color: #aaa;
`;

const MovieInfo = styled.div`
  display: flex;
  justify-content: space-around;
  padding: 12px;
`;

const Button = styled.button`
  background: none;
  border: none;
  cursor: pointer;
  color: ${(props) => (props.active ? "#e50914" : "white")};
  font-size: 18px;
  transition: 0.2s;

  &:hover {
    transform: scale(1.2);
  }
`;

const Count = styled.span`
  color: white;
  font-size: 14px;
`;

/* =========================
   ⚛️ REDUCER (FIXED)
========================= */

const movieReducer = (state, action) => {
  const movie = state[action.id] || {
    isBookmarked: false,
    bookmarkCount: 0,
    isLiked: false,
    likeCount: 0,
  };

  switch (action.type) {
    case "TOGGLE_BOOKMARK":
      return {
        ...state,
        [action.id]: {
          ...movie,
          isBookmarked: !movie.isBookmarked,
          bookmarkCount: movie.isBookmarked
            ? movie.bookmarkCount - 1
            : movie.bookmarkCount + 1,
        },
      };

    case "TOGGLE_LIKE":
      return {
        ...state,
        [action.id]: {
          ...movie,
          isLiked: !movie.isLiked,
          likeCount: movie.isLiked
            ? movie.likeCount - 1
            : movie.likeCount + 1,
        },
      };

    default:
      return state;
  }
};

/* =========================
   🎬 COMPONENT
========================= */

export const MovieList = ({ movies }) => {
  const [state, dispatch] = useReducer(movieReducer, {});

  return (
    <Container>
      <Title>🎬 Movie App</Title>

      <Grid>
        {movies.map((movie) => {
          const movieState = state[movie.id] || {};

          return (
            <MovieCard key={movie.id}>
              <MovieImage src={movie.image} alt={movie.title} />

              <MovieContent>
                <MovieTitle>{movie.title}</MovieTitle>
                <MovieCategory>{movie.category}</MovieCategory>
              </MovieContent>

              <MovieInfo>
                <Button
                  active={movieState.isBookmarked}
                  onClick={() =>
                    dispatch({
                      type: "TOGGLE_BOOKMARK",
                      id: movie.id,
                    })
                  }
                >
                  <FaBookmark />
                </Button>
                <Count>{movieState.bookmarkCount || 0}</Count>

                <Button
                  active={movieState.isLiked}
                  onClick={() =>
                    dispatch({
                      type: "TOGGLE_LIKE",
                      id: movie.id,
                    })
                  }
                >
                  <FaThumbsUp />
                </Button>
                <Count>{movieState.likeCount || 0}</Count>
              </MovieInfo>
            </MovieCard>
          );
        })}
      </Grid>
    </Container>
  );
};

/* =========================
   ✅ PROP TYPES
========================= */

MovieList.propTypes = {
  movies: PropTypes.array,
};

