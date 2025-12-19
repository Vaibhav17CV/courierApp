# 🚚 Courier Delivery Cost Calculator

A PHP-based command-line application that calculates:

- Delivery cost for each package  
- Applicable discounts based on offer codes  
- Estimated delivery time using vehicle scheduling  

This project implements both parts of the courier challenge:

1. Delivery Cost Estimation with Offers  
2. Delivery Time Estimation using multiple vehicles  

---

## 📦 Features

- Accurate delivery cost calculation  
- Validates offer codes based on weight & distance rules  
- Calculates discount only when all criteria match  
- Schedules packages across vehicles based on:
  - Max weight limit
  - Speed
  - Earliest available time  
- Outputs:
  - Discount
  - Total Cost
  - Delivery Time (Hours)
  - Delivery Time (HH:MM:SS)

---

## 🧮 Delivery Cost Formula
-Total Cost = Base Cost + (Weight × 10) + (Distance × 5) - Discount

