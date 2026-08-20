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

// GET active banners by department_id
router.get("/department/:id", async (req, res) => {
  try {
    const deptId = parseInt(req.params.id, 10);
    if (!deptId) return res.json([]);
    const [rows] = await db.query(
      "SELECT * FROM banners WHERE department_id = ? AND is_active = 1 ORDER BY sort_order",
      [deptId]
    );
    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
