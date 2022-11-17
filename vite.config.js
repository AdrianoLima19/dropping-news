import { resolve } from "path";
import { defineConfig } from "vite";

export default defineConfig({
  define: {
    "process.env": process.env,
  },
  root: "./",
  base: "./",
  resolve: {
    alias: {
      "~bootstrap": resolve(__dirname, "node_modules/bootstrap"),
      "~codemirror": resolve(__dirname, "node_modules/codemirror"),
      "~fontawesome": resolve(__dirname, "resources/fontawesome"),
    },
  },
  build: {
    outDir: "resources/dist",
    emptyOutDir: false,
    cssCodeSplit: true,
    lib: {
      entry: [resolve(__dirname, "resources/main.js"), resolve(__dirname, "resources/editor.js")],
      name: "app",
      formats: ["es"],
      fileName: "[name].min",
    },
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          if (/(s[ac]|c)ss/.test(assetInfo.name)) return "[name].min[extname]";
          if (/woff|woff2|eot|ttf/.test(assetInfo.name)) return `fonts/${assetInfo.name}`;

          return assetInfo.name;
        },
      },
    },
  },
});
