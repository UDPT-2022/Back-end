const express = require("express");
const axios = require("axios");
const router = express.Router();

const asyncHandler = require("express-async-handler");

const auth = process.env.AUTH;
const shop = process.env.SHOP;

const options = {
  headers: { Accept: "application/json" },
};

router.put(
  "/:id/admin/accept",
  asyncHandler(async (req, res) => {
    let op = { ...options };
    if (op["headers"]["Authorization"] !== undefined)
      op["headers"]["Authorization"] = req.headers["authorization"];

    let body = {
      NGAY_HIEU_LUC: req.body["NGAY_HIEU_LUC"],
      NGAY_KET_THUC: req.body["NGAY_KET_THUC"],
      HOP_DONG_DA_XET_DUYET: 1,
    };

    let result = null;
    await axios
      .put(auth + "/contract/" + req.params.id, body, op)
      .then((response) => {
        // res.status(200).json(response.data);
        result = response.data;
      })
      .catch((err) => {
        console.log(err.response);
        res.status(err.response.status).json(err.response.data);
        return;
      });

    // console.log(req.headers["authorization"]);
    return res.json(result);
  })
);
router.put(
    "/:id/admin/revoke",
    asyncHandler(async (req, res) => {
      let op = { ...options };
      if (op["headers"]["Authorization"] !== undefined)
        op["headers"]["Authorization"] = req.headers["authorization"];
  
      let body = {
        HOP_DONG_DA_XET_DUYET: 0,
      };
  
      let result = null;
      await axios
        .put(auth + "/contract/" + req.params.id, body, op)
        .then((response) => {
          // res.status(200).json(response.data);
          result = response.data;
        })
        .catch((err) => {
          console.log(err.response);
          res.status(err.response.status).json(err.response.data);
          return;
        });
  
      // console.log(req.headers["authorization"]);
      return res.json(result);
    })
  );
module.exports = router;
