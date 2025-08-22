<template>
  <a-form ref="formRef" :model="form" :rules="rules" layout="vertical">
    <a-tabs default-active-key="1">
      <a-tab-pane key="1" title="基本信息">
        <a-row :gutter="16">
          <a-col :span="12">
            <a-form-item field="name" label="商品名称">
              <a-input v-model="form.name" placeholder="请输入商品名称" />
            </a-form-item>
          </a-col>
          <a-col :span="12">
            <a-form-item field="sn" label="商品编号">
              <a-input v-model="form.sn" placeholder="请输入商品编号 (留空自动生成)" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-form-item field="description" label="商品描述">
          <a-textarea v-model="form.description" placeholder="请输入商品描述" :auto-size="{ minRows: 3, maxRows: 5 }" />
        </a-form-item>
        <a-row :gutter="16">
            <a-col :span="12">
                <a-form-item field="image" label="商品主图">
                    <ma-upload-image v-model="form.image" />
                </a-form-item>
            </a-col>
            <a-col :span="12">
                 <a-form-item field="images" label="商品图集">
                    <ma-upload-image v-model="form.images" multiple />
                </a-form-item>
            </a-col>
        </a-row>
      </a-tab-pane>
      <a-tab-pane key="2" title="销售信息">
         <a-row :gutter="16">
          <a-col :span="8">
            <a-form-item field="price" label="商品价格">
              <a-input-number v-model="form.price" placeholder="请输入价格" :min="0" :precision="2">
                <template #prepend>¥</template>
              </a-input-number>
            </a-form-item>
          </a-col>
          <a-col :span="8">
            <a-form-item field="original_price" label="原价">
              <a-input-number v-model="form.original_price" placeholder="请输入原价" :min="0" :precision="2">
                 <template #prepend>¥</template>
              </a-input-number>
            </a-form-item>
          </a-col>
           <a-col :span="8">
            <a-form-item field="stock" label="库存">
              <a-input-number v-model="form.stock" placeholder="请输入库存" :min="0" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="16">
           <a-col :span="8">
            <a-form-item field="unit" label="单位">
              <a-input v-model="form.unit" placeholder="如: 件、个、台" />
            </a-form-item>
          </a-col>
           <a-col :span="8">
            <a-form-item field="weight" label="重量">
              <a-input-number v-model="form.weight" placeholder="请输入重量" :min="0" :precision="2">
                <template #append>KG</template>
              </a-input-number>
            </a-form-item>
          </a-col>
        </a-row>
      </a-tab-pane>
       <a-tab-pane key="3" title="其他信息">
          <a-row :gutter="16">
            <a-col :span="8">
                <a-form-item field="sort" label="排序">
                    <a-input-number v-model="form.sort" placeholder="数字越大越靠前" />
                </a-form-item>
            </a-col>
            <a-col :span="8">
                <a-form-item field="is_hot" label="是否热门">
                    <a-switch v-model="form.is_hot" :checked-value="1" :unchecked-value="0" />
                </a-form-item>
            </a-col>
             <a-col :span="8">
                <a-form-item field="is_recommend" label="是否推荐">
                    <a-switch v-model="form.is_recommend" :checked-value="1" :unchecked-value="0" />
                </a-form-item>
            </a-col>
          </a-row>
       </a-tab-pane>
    </a-tabs>
  </a-form>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Product } from '../../api/types';
import { FieldRule } from '@arco-design/web-vue';

const props = defineProps<{
  modelValue: Partial<Product>;
}>();

const form = ref<Partial<Product>>({});
const formRef = ref();

watch(() => props.modelValue, (val) => {
  if (val) {
    form.value = { ...val };
  }
}, { immediate: true, deep: true });

const rules: Record<string, FieldRule[]> = {
  name: [{ required: true, message: '商品名称不能为空' }],
  price: [{ required: true, message: '商品价格不能为空' }],
  stock: [{ required: true, message: '库存不能为空' }],
  image: [{ required: true, message: '请上传商品主图' }],
};

const validate = async () => {
    const err = await formRef.value.validate();
    if (err) {
        return false;
    }
    return form.value;
}

defineExpose({
    validate
})
</script>
