const purgecss = require("@fullhuman/postcss-purgecss");

module.exports = {
  plugins: [
    // purgecss({
    //   content: ["./views/*.php"],
    // }),
    require("autoprefixer")({}),
  ],
};
