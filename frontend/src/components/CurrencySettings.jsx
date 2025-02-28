import React, { useState, useEffect } from 'react';
import axios from 'axios';

const CurrencySettings = ({ visible, onClose, onSave }) => {
  const [allCurrencies, setAllCurrencies] = useState([]);
  const [selected, setSelected] = useState({});
  const [searchTerm, setSearchTerm] = useState('');

  const filteredCurrencies = allCurrencies.filter(currency => {
    const term = searchTerm.toLowerCase();
    return (
      currency.name.toLowerCase().includes(term) ||
      currency.code.toLowerCase().includes(term)
    );
  });

  useEffect(() => {
    const fetchCurrencies = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/currencies/list.json');
        setAllCurrencies(response.data.currencies);
        
        // Загрузка сохраненных настроек
        const saved = JSON.parse(localStorage.getItem('currencySettings') || '{}');
        setSelected(saved);
      } catch (error) {
        console.error('Error loading currencies:', error);
      }
    };
    
    fetchCurrencies();
  }, []);

  const toggleCurrency = (code) => {
    setSelected(prev => ({ ...prev, [code]: !prev[code] }));
  };

  const handleSave = () => {
    localStorage.setItem('currencySettings', JSON.stringify(selected));
    onSave(selected);
    onClose();
  };

  if (!visible) return null;

  return (
    <div className="settings-modal">
      <div className="settings-content">
        <h3>Выберите валюты</h3>
        <input
          type="text"
          placeholder="Поиск по названию или коду..."
          className="search-input"
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
        />
        <div className="currencies-list">
          {filteredCurrencies.map(currency => (
            <label key={currency.code} className="currency-checkbox">
              <input
                type="checkbox"
                checked={selected[currency.code] ?? true}
                onChange={() => toggleCurrency(currency.code)}
              />
              {currency.name} ({currency.code})
            </label>
          ))}
        </div>
        <div className="settings-actions">
          <button onClick={handleSave}>Сохранить</button>
          <button onClick={onClose}>Отмена</button>
        </div>
      </div>
    </div>
  );
};

export default CurrencySettings;