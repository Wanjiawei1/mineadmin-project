import { request } from '@/utils/request';
import { PageResponse, Result } from '@/types/result';
import { Product, ProductQuery, ProductStats } from './types';

/**
 * 获取商品分页列表
 * @param params
 * @returns
 */
export function getProductPage(params: ProductQuery): Promise<PageResponse<Product>> {
  return request.get('/admin/product/list', { params });
}

/**
 * 获取商品详情
 * @param id
 * @returns
 */
export function getProduct(id: number): Promise<Result<Product>> {
  return request.get(`/admin/product/${id}`);
}

/**
 * 创建商品
 * @param data
 * @returns
 */
export function createProduct(data: Partial<Product>): Promise<Result<Product>> {
  return request.post('/admin/product', data);
}

/**
 * 更新商品
 * @param id
 * @param data
 * @returns
 */
export function updateProduct(id: number, data: Partial<Product>): Promise<Result<Product>> {
  return request.put(`/admin/product/${id}`, data);
}

/**
 * 删除商品
 * @param id
 * @returns
 */
export function deleteProduct(id: number): Promise<Result<null>> {
  return request.delete(`/admin/product/${id}`);
}

/**
 * 批量删除商品
 * @param ids
 * @returns
 */
export function batchDeleteProduct(ids: number[]): Promise<Result<{ count: number }>> {
  return request.post('/admin/product/batch/delete', { ids });
}

/**
 * 上架商品
 * @param id
 * @returns
 */
export function onShelfProduct(id: number): Promise<Result<null>> {
  return request.post(`/admin/product/${id}/on-shelf`);
}

/**
 * 下架商品
 * @param id
 * @returns
 */
export function offShelfProduct(id: number): Promise<Result<null>> {
  return request.post(`/admin/product/${id}/off-shelf`);
}

/**
 * 批量上架商品
 * @param ids
 * @returns
 */
export function batchOnShelfProduct(ids: number[]): Promise<Result<{ count: number }>> {
  return request.post('/admin/product/batch/on-shelf', { ids });
}

/**
 * 批量下架商品
 * @param ids
 * @returns
 */
export function batchOffShelfProduct(ids: number[]): Promise<Result<{ count: number }>> {
  return request.post('/admin/product/batch/off-shelf', { ids });
}

/**
 * 调整库存
 * @param id
 * @param quantity
 * @param type
 * @returns
 */
export function adjustStock(id: number, quantity: number, type: 'increase' | 'decrease'): Promise<Result<null>> {
  return request.post(`/admin/product/${id}/adjust-stock`, { quantity, type });
}

/**
 * 获取商品统计信息
 * @returns
 */
export function getProductStats(): Promise<Result<ProductStats>> {
  return request.get('/admin/product/stats');
}

/**
 * 获取低库存商品
 * @param threshold
 * @returns
 */
export function getLowStockProducts(threshold: number): Promise<Result<Product[]>> {
  return request.get('/admin/product/low-stock', { params: { threshold } });
}

/**
 * 获取商品状态选项
 * @returns
 */
export function getProductStatusOptions(): Promise<Result<Record<string, string>>> {
  return request.get('/admin/product/status-options');
}
