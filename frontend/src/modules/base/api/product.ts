/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PageList, ResponseStruct } from '#/global'

export interface ProductVo {
  id?: number
  name?: string
  sn?: string
  description?: string
  content?: string
  image?: string
  images?: string[]
  category_id?: number
  price?: number
  original_price?: number
  stock?: number
  sales?: number
  status?: 1 | 2 | 3  // 1-下架，2-上架，3-售罄
  status_label?: string
  sort?: number
  weight?: number
  unit?: string
  specs?: Record<string, any>
  attributes?: Record<string, any>
  is_virtual?: 0 | 1
  is_hot?: 0 | 1
  is_recommend?: 0 | 1
  shelf_time?: string
  created_by?: number
  updated_by?: number
  created_at?: string
  updated_at?: string
  remark?: string
}

export interface ProductSearchVo {
  name?: string
  sn?: string
  status?: number
  category_id?: number
  keyword?: string
  start_time?: string
  end_time?: string
  is_hot?: number
  is_recommend?: number
}

export interface ProductStatsVo {
  total_sales?: number
  on_shelf_count?: number
  off_shelf_count?: number
  sold_out_count?: number
  total_count?: number
}

export interface StatusOption {
  value: number
  label: string
  color: string
}

export function page(data: ProductSearchVo): Promise<ResponseStruct<PageList<ProductVo>>> {
  return useHttp().get('/admin/product/list', { params: data })
}

export function create(data: ProductVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/product', data)
}

export function save(id: number, data: ProductVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/product/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/product/batch/delete', { data: { ids } })
}

export function deleteById(id: number): Promise<ResponseStruct<null>> {
  return useHttp().delete(`/admin/product/${id}`)
}

export function getById(id: number): Promise<ResponseStruct<ProductVo>> {
  return useHttp().get(`/admin/product/${id}`)
}

export function onShelf(id: number): Promise<ResponseStruct<null>> {
  return useHttp().post(`/admin/product/${id}/on-shelf`)
}

export function offShelf(id: number): Promise<ResponseStruct<null>> {
  return useHttp().post(`/admin/product/${id}/off-shelf`)
}

export function batchOnShelf(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/product/batch/on-shelf', { ids })
}

export function batchOffShelf(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/product/batch/off-shelf', { ids })
}

export function adjustStock(id: number, quantity: number, type: 'increase' | 'decrease'): Promise<ResponseStruct<null>> {
  return useHttp().post(`/admin/product/${id}/adjust-stock`, { quantity, type })
}

export function getStats(): Promise<ResponseStruct<ProductStatsVo>> {
  return useHttp().get('/admin/product/stats')
}

export function getLowStock(threshold?: number): Promise<ResponseStruct<ProductVo[]>> {
  return useHttp().get('/admin/product/low-stock', { params: { threshold } })
}

export function getStatusOptions(): Promise<ResponseStruct<StatusOption[]>> {
  return useHttp().get('/admin/product/status-options')
}

