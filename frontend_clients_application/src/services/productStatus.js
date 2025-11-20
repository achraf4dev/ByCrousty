import api from '../services/api';

/**
 * Utility to check if a product is active (exists and status is 'active')
 * @param {Number} productId
 * @returns {Promise<Boolean>}
 */
export async function isProductActive(productId) {
  try {
    const response = await api.getProduct(productId);
    // The backend returns product data inside response.data.data
    return response.data && response.data.data && response.data.data.status === 'active';
  } catch (e) {
    return false;
  }
}
