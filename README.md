Advanced Crop Recommendation Project (MySQL + Admin + Charts)
Location: /mnt/data/crop_reco_advanced

Quick start (Windows + XAMPP):
1. Extract this folder to C:\xampp\htdocs\crop_reco_advanced\
2. Start Apache and MySQL from XAMPP control panel.
3. Open phpMyAdmin and run the SQL in web/schema.sql to create database/table.
4. Edit web/config.php to set DB credentials and change admin password.
5. Install Python and required libraries:
   pip install numpy pandas scikit-learn joblib
6. Train model (in ml/ folder):
   python train_model.py
   This will create ml/model.pkl
7. Test prediction manually:
   python ml/predict.py 90 42 43 20.5 85 6.5 200
8. Open in browser:
   http://localhost/crop_reco_advanced/web/index.php

Notes:
- The Python model training uses scikit-learn RandomForest and saves a joblib file model.pkl.
- PHP calls predict.py and expects JSON response.
- Admin panel available at /web/admin.php (default admin/admin123). Change the password in config.php.