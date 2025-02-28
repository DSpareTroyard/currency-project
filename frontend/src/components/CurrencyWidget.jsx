import React, { useState, useEffect } from 'react';
import axios from 'axios';
import CurrencySettings from './CurrencySettings';

const CurrencyWidget = () => {
  const [currencies, setCurrencies] = useState([]);
  const [updateInterval, setRefreshInterval] = useState(1000);
  const [settingsVisible, setSettingsVisible] = useState(false);
  const [selectedCurrencies, setSelectedCurrencies] = useState({});

  useEffect(() => {
    const saved = JSON.parse(localStorage.getItem('currencySettings') || '{}');
    setSelectedCurrencies(saved);
  }, []);

  const fetchData = async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/currencies.json');
      const filtered = response.data.currencies.filter(c => 
        selectedCurrencies[c.code] ?? true
      );
      setCurrencies(filtered);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };

  useEffect(() => {
    fetchData();
    const interval = setInterval(fetchData, 1000);
    return () => clearInterval(interval);
  }, [selectedCurrencies]);

  return (
    <div className="currency-widget">
      <div className="widget-header">
        <h2>Курсы валют</h2>
        <button 
          onClick={() => setSettingsVisible(true)}
          className="settings-button"
        >
          ⚙️ Настройки
        </button>
      </div>

      <CurrencySettings
        visible={settingsVisible}
        onClose={() => setSettingsVisible(false)}
        onSave={(settings) => setSelectedCurrencies(settings)}
      />

      <div className="controls">
        <select 
            value={updateInterval} 
            onChange={(e) => setRefreshInterval(Number(e.target.value))}
          >
            <option value={1000}>1 сек</option>
            <option value={30000}>30 сек</option>
            <option value={60000}>1 мин</option>
            <option value={300000}>5 мин</option>
          </select>
      </div>
      <div className="rates-list">
      {currencies.map(currency => (
        <div 
          key={currency.id} 
          className="rate-item"
        >
          <div className="currency-info">
            <span className="currency-name">{currency.name}</span>
            <span className="currency-code">{currency.code}</span>
            <span className="currency-nominal">
              за {currency.nominal} ед.
            </span>
          </div>
          <div className="rate-values">
            <span className="rate-per-unit">
              {currency.rate ? parseFloat(currency.rate).toFixed(4) : 'N/A'} руб.
            </span>
            {currency.change && (
              <span className={`change ${currency.change}`}>
                {currency.change === 'up' ? '↑' : currency.change === 'down' ? '↓' : '='}
              </span>
            )}
          </div>
        </div>
      ))}
      </div>
    </div>
  );
};

export default CurrencyWidget;