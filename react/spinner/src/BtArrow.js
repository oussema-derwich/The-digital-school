import React from "react";
<script src="https://kit.fontawesome.com/23df55ab57.js" crossorigin="anonymous"></script>
const BtArrow = ({ dispatch, alt, type }) => {
    return (
        <img src="/Screenshot_2024-05-07_at_14-26-14_Font_Awesome-removebg-preview.png" className={["arrow, type"].join(" ")}
        alt={alt}
        onClick={() =>
            dispatch({
                type: type === "top" ? "DECREMENT" : "INCREMENT"})
        }
        />
    );
};

export default BtArrow;