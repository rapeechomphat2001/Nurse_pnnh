const express = require("express");
const router = express.Router();
const db = require("../config/db");

// GET all active banners
router.get("/", async (req, res) => {
  try {
    const [rows] = await db.query(
      "SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order"
    );
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
