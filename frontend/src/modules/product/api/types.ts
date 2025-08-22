export interface Product {
  id: number;
  name: string;
  sn: string;
  description?: string;
  content?: string;
  image: string;
  images?: string[];
  category_id?: number;
  price: number;
  original_price?: number;
  stock: number;
  sales: number;
  status: number;
  status_label?: string;
  sort: number;
  weight?: number;
  unit?: string;
  specs?: Record<string, any>[];
  attributes?: Record<string, any>[];
  is_virtual: number;
  is_hot: number;
  is_recommend: number;
  shelf_time?: string;
  created_by: number;
  updated_by?: number;
  created_at: string;
  updated_at: string;
  remark?: string;
}

export interface ProductQuery extends PageQuery {
  status?: number;
  category_id?: number;
  keyword?: string;
  created_by?: number;
  start_time?: string;
  end_time?: string;
}

export interface ProductStats {
  total_sales: number;
  on_shelf_count: number;
  off_shelf_count: number;
  sold_out_count: number;
  total_count: number;
}
