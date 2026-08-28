const path = require("path");
const express = require("express");
const cors = require("cors");
require("dotenv").config({ path: path.join(__dirname, "..", ".env") });

const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || "0.0.0.0";
const configuredOrigins = (process.env.CORS_ORIGINS || "")
  .split(",")
  .map((origin) => origin.trim())
  .filter(Boolean);

function isAllowedOrigin(origin) {
  if (configuredOrigins.includes(origin)) return true;

  try {
    const url = new URL(origin);
    const isLocalHost = ["localhost", "127.0.0.1"].includes(url.hostname);
    const isLocalTestDomain = url.hostname.endsWith(".test");
    const isLanIP = /^192\.168\.\d+\.\d+$/.test(url.hostname)
      || /^10\.\d+\.\d+\.\d+$/.test(url.hostname)
      || /^172\.(1[6-9]|2\d|3[01])\.\d+\.\d+$/.test(url.hostname);

    return ["http:", "https:"].includes(url.protocol) &&
      (isLocalHost || isLocalTestDomain || isLanIP);
  } catch {
    return false;
  }
}

app.disable("x-powered-by");
app.use(cors({
  origin(origin, callback) {
    callback(null, !origin || isAllowedOrigin(origin));
  },
  methods: ["GET", "HEAD", "OPTIONS"],
  optionsSuccessStatus: 204,
}));

app.use("/api", (req, res, next) => {
  if (["GET", "HEAD", "OPTIONS"].includes(req.method)) return next();

  res.set("Allow", "GET, HEAD, OPTIONS");
  return res.status(405).json({ error: "Method not allowed" });
});

app.use("/api/news", require("./routes/news.routes"));
app.use("/api/departments", require("./routes/departments.routes"));
app.use("/api/banners", require("./routes/banners.routes"));
app.use("/api/general-contents", require("./routes/general-contents.routes"));

app.get("/", (req, res) => {
  res.json({ message: "Pakchong Nana Hospital API", status: "running" });
});

app.listen(PORT, HOST, () => {
  console.log(`Server running on http://${HOST}:${PORT}`);
});
