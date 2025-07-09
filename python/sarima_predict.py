import sys
import json
import pandas as pd
import numpy as np
from statsmodels.tsa.statespace.sarimax import SARIMAX
from statsmodels.tsa.stattools import adfuller
from sklearn.metrics import mean_absolute_error
import warnings
import traceback
from pmdarima import auto_arima

class NumpyEncoder(json.JSONEncoder):
    """ Custom encoder for numpy data types """
    def default(self, obj):
        if isinstance(obj, (np.integer, np.int64)):
            return int(obj)
        elif isinstance(obj, (np.floating, np.float64)):
            return float(obj)
        elif isinstance(obj, np.ndarray):
            return obj.tolist()
        elif isinstance(obj, (np.bool_, bool)):
            return bool(obj)
        return super().default(obj)

def main():
    response = {
        'status': 'error',
        'message': 'Uninitialized',
        'forecast': {},
        'confidence_intervals': {},
        'model_stats': {}
    }

    try:
        # 1. Input validation
        if len(sys.argv) < 2:
            raise ValueError("Missing input file path")
        
        with open(sys.argv[1], 'r', encoding='utf-8') as f:
            input_data = json.load(f)
        
        if not isinstance(input_data.get('historical_data'), list):
            raise ValueError("Invalid historical data format")

        # 2. Data preparation
        df = pd.DataFrame(input_data['historical_data'])
        df['date'] = pd.to_datetime(
            df['tahun'].astype(str) + '-' + 
            df['bulan'].astype(str).str.zfill(2) + '-01',
            errors='coerce'
        )
        df = df.dropna(subset=['date'])
        df.set_index('date', inplace=True)
        ts = df['total'].astype(float)
        
        if len(ts) < 12:
            raise ValueError("At least 12 months of data required")

        # 3. Stationarity check
        adf_result = adfuller(ts)
        is_stationary = bool(adf_result[1] < 0.05)  # Convert to native Python bool
        d = 0 if is_stationary else 1

        # 4. Parameter selection with auto_arima
        seasonal_period = 12  # Monthly data
        
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            auto_model = auto_arima(
                ts,
                seasonal=True,
                m=seasonal_period,
                suppress_warnings=True,
                error_action='ignore',
                stepwise=True,
                trace=False
            )
            
            best_order = tuple(map(int, auto_model.order))  # Convert to regular integers
            best_seasonal_order = tuple(map(int, auto_model.seasonal_order))  # Convert to regular integers

        # 5. Model training
        with warnings.catch_warnings():
            warnings.simplefilter("ignore")
            model = SARIMAX(
                ts,
                order=best_order,
                seasonal_order=best_seasonal_order,
                enforce_stationarity=False,
                enforce_invertibility=False
            )
            model_fit = model.fit(disp=False)

        # 6. Forecasting with confidence intervals
        forecast_periods = input_data.get('forecast_periods', 12)
        forecast = model_fit.get_forecast(steps=forecast_periods)
        
        # Convert predictions to integers
        predictions = forecast.predicted_mean.round().astype(int)
        conf_int = forecast.conf_int().round().astype(int)
        
        # 7. Model evaluation
        train_pred = model_fit.predict(start=0, end=len(ts)-1)
        mae = float(mean_absolute_error(ts, train_pred))  # Convert to native float

        # 8. Prepare output - ensure all values are JSON-serializable
        response.update({
            'status': 'success',
            'forecast': {
                i+1: int(val) for i, val in enumerate(predictions)
            },
            'confidence_intervals': {
                i+1: [int(conf_int.iloc[i,0]), int(conf_int.iloc[i,1])] 
                for i in range(forecast_periods)
            },
            'model_stats': {
                'order': list(best_order),
                'seasonal_order': list(best_seasonal_order),
                'aic': float(model_fit.aic),
                'bic': float(model_fit.bic),
                'mae': mae,
                'is_stationary': is_stationary,
                'adf_pvalue': float(adf_result[1]),
                'training_data_points': int(len(ts))
            }
        })

    except Exception as e:
        response.update({
            'message': str(e),
            'trace': traceback.format_exc()
        })
    
    # Output with custom encoder
    print(json.dumps(response, cls=NumpyEncoder, ensure_ascii=False))

if __name__ == "__main__":
    main()