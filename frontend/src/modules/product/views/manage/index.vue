<template>
  <div class="p-4">
    <a-card :bordered="false">
      <!-- Search Form -->
      <a-form ref="searchFormRef" :model="query" layout="inline" class="mb-4">
        <a-form-item field="keyword" label="关键词">
          <a-input v-model="query.keyword" placeholder="输入商品名称/编号搜索" @press-enter="search" />
        </a-form-item>
        <a-form-item field="status" label="状态">
          <a-select v-model="query.status" placeholder="请选择商品状态" allow-clear>
            <a-option v-for="(label, value) in statusOptions" :key="value" :value="Number(value)">{{ label }}</a-option>
          </a-select>
        </a-form-item>
        <a-form-item>
          <a-space>
            <a-button type="primary" @click="search">
              <template #icon><icon-search /></template>查询
            </a-button>
            <a-button @click="resetQuery">
              <template #icon><icon-refresh /></template>重置
            </a-button>
          </a-space>
        </a-form-item>
      </a-form>

      <!-- Action Buttons -->
      <a-row class="mb-4">
        <a-col :span="12">
          <a-space>
            <a-button type="primary" @click="handleAdd" v-permission="['product:create']">
              <template #icon><icon-plus /></template>新增
            </a-button>
            <a-button type="primary" status="danger" @click="handleBatchDelete" v-permission="['product:delete']">
              <template #icon><icon-delete /></template>删除
            </a-button>
             <a-button @click="handleBatchOnShelf" v-permission="['product:shelf']">上架</a-button>
            <a-button status="warning" @click="handleBatchOffShelf" v-permission="['product:shelf']">下架</a-button>
          </a-space>
        </a-col>
      </a-row>

      <!-- Data Table -->
      <a-table
        row-key="id"
        :data="data"
        :loading="loading"
        :pagination="pagination"
        :row-selection="{ type: 'checkbox', showCheckedAll: true }"
        v-model:selected-keys="selectedKeys"
        @page-change="handlePageChange"
        @page-size-change="handlePageSizeChange"
      >
        <template #columns>
          <a-table-column title="商品名称" data-index="name" :width="200" ellipsis tooltip />
          <a-table-column title="商品编号" data-index="sn" :width="180" />
          <a-table-column title="主图" :width="100">
             <template #cell="{ record }">
               <a-image :src="record.image" width="60" height="60" fit="cover" />
             </template>
          </a-table-column>
          <a-table-column title="价格" data-index="price" :width="120">
             <template #cell="{ record }">
                <span>¥ {{ record.price.toFixed(2) }}</span>
             </template>
          </a-table-column>
          <a-table-column title="库存" data-index="stock" :width="100" />
          <a-table-column title="销量" data-index="sales" :width="100" />
          <a-table-column title="状态" :width="120">
             <template #cell="{ record }">
               <a-tag :color="record.status === 1 ? 'green' : record.status === 2 ? 'red' : 'gray'">
                 {{ record.status_label }}
               </a-tag>
             </template>
          </a-table-column>
          <a-table-column title="创建时间" data-index="created_at" :width="180" />
          <a-table-column title="操作" fixed="right" :width="220">
            <template #cell="{ record }">
              <a-space>
                <a-button size="small" @click="handleEdit(record)" v-permission="['product:update']">编辑</a-button>
                <a-popconfirm content="确定要删除此商品吗？" @ok="handleDelete(record.id)">
                   <a-button size="small" status="danger" v-permission="['product:delete']">删除</a-button>
                </a-popconfirm>
                <a-button size="small" v-if="record.status !== 1" @click="handleOnShelf(record.id)" v-permission="['product:shelf']">上架</a-button>
                <a-button size="small" status="warning" v-if="record.status === 1" @click="handleOffShelf(record.id)" v-permission="['product:shelf']">下架</a-button>
              </a-space>
            </template>
          </a-table-column>
        </template>
      </a-table>
    </a-card>

    <!-- Add/Edit Drawer -->
    <a-drawer :width="800" :visible="drawerVisible" @ok="handleDrawerOk" @cancel="handleDrawerCancel" unmount-on-close :title="isEdit ? '编辑商品' : '新增商品'">
       <product-form v-if="drawerVisible" ref="productFormRef" v-model="currentProduct" />
    </a-drawer>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import {
  getProductPage,
  deleteProduct,
  batchDeleteProduct,
  getProductStatusOptions,
  onShelfProduct,
  offShelfProduct,
  batchOnShelfProduct,
  batchOffShelfProduct,
  getProduct,
  createProduct,
  updateProduct,
} from '../api/product';
import { Product, ProductQuery } from '../api/types';
import { Message, Modal } from '@arco-design/web-vue';
import { usePagination } from '@/hooks';
import ProductForm from './components/ProductForm.vue';

const loading = ref(false);
const data = ref<Product[]>([]);
const selectedKeys = ref<number[]>([]);
const statusOptions = ref<Record<string, string>>({});

// Drawer state
const drawerVisible = ref(false);
const isEdit = ref(false);
const currentProduct = ref<Partial<Product>>({});
const productFormRef = ref();

const { pagination, setTotal } = usePagination(() => fetchData());

const query = reactive<ProductQuery>({
  page: 1,
  pageSize: 20,
  keyword: '',
  status: undefined,
});

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await getProductPage({ ...query, page: pagination.current, pageSize: pagination.pageSize });
    data.value = res.data.items;
    setTotal(res.data.total);
  } catch (err) {
    // handle error
  } finally {
    loading.value = false;
  }
};

const fetchStatusOptions = async () => {
  try {
    const res = await getProductStatusOptions();
    statusOptions.value = res.data;
  } catch (error) {
     //
  }
}

const search = () => {
  pagination.current = 1;
  fetchData();
};

const resetQuery = () => {
  query.keyword = '';
  query.status = undefined;
  search();
};

const handlePageChange = (page: number) => {
  pagination.current = page;
  fetchData();
};

const handlePageSizeChange = (pageSize: number) => {
  pagination.pageSize = pageSize;
  fetchData();
};

const handleAdd = () => {
  isEdit.value = false;
  currentProduct.value = {
    unit: '件',
    sort: 0,
    is_hot: 0,
    is_recommend: 0,
    stock: 0,
    price: 0.00,
    original_price: 0.00,
  };
  drawerVisible.value = true;
};

const handleEdit = async (record: Product) => {
  isEdit.value = true;
  try {
    const res = await getProduct(record.id);
    currentProduct.value = res.data;
    drawerVisible.value = true;
  } catch (error) {
    //
  }
};

const handleDelete = async (id: number) => {
  try {
    await deleteProduct(id);
    Message.success('删除成功');
    fetchData();
  } catch (err) {
    //
  }
};

const handleBatchDelete = () => {
  if (selectedKeys.value.length === 0) {
    Message.warning('请至少选择一项');
    return;
  }
  Modal.confirm({
    title: '确认删除',
    content: `确定要删除选中的 ${selectedKeys.value.length} 个商品吗？`,
    onOk: async () => {
      try {
        const res = await batchDeleteProduct(selectedKeys.value);
        Message.success(`成功删除 ${res.data.count} 个商品`);
        selectedKeys.value = [];
        fetchData();
      } catch (err) {
        //
      }
    }
  })
};

const handleOnShelf = async (id: number) => {
    try {
        await onShelfProduct(id);
        Message.success('上架成功');
        fetchData();
    } catch (error) {
        //
    }
}
const handleOffShelf = async (id: number) => {
    try {
        await offShelfProduct(id);
        Message.success('下架成功');
        fetchData();
    } catch (error) {
        //
    }
}

const handleBatchOnShelf = async () => {
  if (selectedKeys.value.length === 0) {
    Message.warning('请至少选择一项');
    return;
  }
  try {
    const res = await batchOnShelfProduct(selectedKeys.value);
    Message.success(`成功上架 ${res.data.count} 个商品`);
    selectedKeys.value = [];
    fetchData();
  } catch (err) {
    //
  }
}

const handleBatchOffShelf = async () => {
  if (selectedKeys.value.length === 0) {
    Message.warning('请至少选择一项');
    return;
  }
  try {
    const res = await batchOffShelfProduct(selectedKeys.value);
    Message.success(`成功下架 ${res.data.count} 个商品`);
    selectedKeys.value = [];
    fetchData();
  } catch (err) {
    //
  }
}

const handleDrawerOk = async () => {
  const formData = await productFormRef.value?.validate();
  if (!formData) {
    return;
  }
  try {
    if (isEdit.value) {
      await updateProduct(currentProduct.value.id!, formData);
      Message.success('更新成功');
    } else {
      await createProduct(formData);
      Message.success('创建成功');
    }
    drawerVisible.value = false;
    fetchData();
  } catch (error) {
    //
  }
}

const handleDrawerCancel = () => {
  drawerVisible.value = false;
}

onMounted(() => {
  fetchData();
  fetchStatusOptions();
});
</script>

<style scoped>
.action-buttons {
  margin-bottom: 16px;
}
</style>
