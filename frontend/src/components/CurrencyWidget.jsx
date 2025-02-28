import React, { useState, useEffect } from 'react';
import axios from 'axios';

const CurrencyWidget = () => {
  const [currencies, setCurrencies] = useState([]);
  const [refreshInterval, setRefreshInterval] = useState(60); // секунды

  const fetchData = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/currencies.json');
      setCurrencies(response.data.currencies);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  useEffect(() => {
    fetchData();
    const interval = setInterval(fetchData, refreshInterval * 1000);
    return () => clearInterval(interval);
  }, [refreshInterval]);

  return (
    <div className="currency-widget">
      <h2>Курсы валют ЦБ РФ</h2>
      <div className="controls">
        <select 
          value={refreshInterval} 
          onChange={(e) => setRefreshInterval(Number(e.target.value))}
        >
          <option value={30}>30 сек</option>
          <option value={60}>1 мин</option>
          <option value={300}>5 мин</option>
        </select>
      </div>
      <div className="rates-list">
        {currencies.map(currency => (
          <div key={currency.id} className="rate-item">
            <span className="currency-code">{currency.code}</span>
            <span className="rate-value">{currency.rate}</span>
            <span className={`change ${currency.change}`}>
              {currency.change === 'up' ? '↑' : '↓'}
            </span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default CurrencyWidget;