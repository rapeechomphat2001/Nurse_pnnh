const express = require("express");
const router = express.Router();
const db = require("../config/db");

// GET all news
router.get("/", async (req, res) => {
  try {
    const [rows] = await db.query(
      "SELECT * FROM news ORDER BY created_at DESC"
    );
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// GET single news by id
router.get("/:id", async (req, res) => {
  try {
    const [rows] = await db.query("SELECT * FROM news WHERE id = ?", [
      req.params.id,
    ]);
    if (rows.length === 0) return res.status(404).json({ error: "Not found" });
    res.json(rows[0]);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
