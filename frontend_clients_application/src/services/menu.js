import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';

export async function getMenu() {
  const response = await axios.get(`${API_BASE_URL}/menu`);
  return response.data.data;
}
