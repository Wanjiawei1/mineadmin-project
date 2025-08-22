<!--
 - MineAdmin is committed to providing solutions for quickly building web applications
 - Please view the LICENSE file that was distributed with this source code,
 - For the full copyright and license information.
 - Thank you very much for using MineAdmin.
 -
 - @Author X.Mo<root@imoi.cn>
 - @Link   https://github.com/mineadmin
-->
<script setup lang="tsx">
import type { MaProTableExpose, MaProTableOptions, MaProTableSchema } from '@mineadmin/pro-table'
import type { Ref } from 'vue'
import type { TransType } from '@/hooks/auto-imports/useTrans.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { deleteByIds, page, onShelf, offShelf, batchOnShelf, batchOffShelf, adjustStock } from '~/base/api/product'
import getSearchItems from './data/getSearchItems.tsx'
import getTableColumns from './data/getTableColumns.tsx'
import useDialog from '@/hooks/useDialog.ts'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'

import ProductForm from './form.vue'
import StockAdjustForm from './stockAdjustForm.vue'

defineOptions({ name: 'product:manage' })

const proTableRef = ref<MaProTableExpose>() as Ref<MaProTableExpose>
const formRef = ref()
const stockRef = ref()
const selections = ref<any[]>([])
const i18n = useTrans() as TransType
const t = i18n.globalTrans
const msg = useMessage()

// 弹窗配置
const maDialog: UseDialogExpose = useDialog({
  lgWidth: '900px',
  // 保存数据
  ok: ({ formType }, okLoadingState: (state: boolean) => void) => {
    okLoadingState(true)
    if (['add', 'edit'].includes(formType)) {
      const elForm = formRef.value.maForm.getElFormRef()
      // 验证通过后
      elForm.validate().then(() => {
        switch (formType) {
          // 新增
          case 'add':
            formRef.value.add().then((res: any) => {
              res.code === ResultCode.SUCCESS ? msg.success('商品创建成功') : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
          // 修改
          case 'edit':
            formRef.value.edit().then((res: any) => {
              res.code === 200 ? msg.success('商品更新成功') : msg.error(res.message)
              maDialog.close()
              proTableRef.value.refresh()
            }).catch((err: any) => {
              msg.alertError(err)
            })
            break
        }
      }).catch()
    }
    else if (formType === 'stock') {
      const elForm = stockRef.value.maForm.getElFormRef()
      elForm.validate().then(() => {
        stockRef.value.adjustStock().then((res: any) => {
          res.code === ResultCode.SUCCESS ? msg.success(res.message || '库存调整成功') : msg.error(res.message)
          maDialog.close()
          proTableRef.value.refresh()
        }).catch((err: any) => {
          msg.alertError(err)
        })
      }).catch()
    }
  },
})

const options: MaProTableOptions = {
  api: page,
  showIndex: false,
  rowSelection: {
    showCheckedAll: true,
    onSelectionChange: (changeableRowKeys: string[]) => {
      selections.value = changeableRowKeys
    },
  },
  showTools: true,
  toolButton: [
    {
      text: '新增',
      type: 'primary',
      props: { type: 'primary', icon: 'material-symbols:add' },
      click: () => {
        maDialog.open({ title: '新增商品', formType: 'add' })
      },
      show: () => hasAuth(['product:create']),
    },
    {
      text: '批量上架',
      type: 'primary',
      props: { type: 'success' },
      click: () => {
        if (selections.value.length === 0) {
          msg.warning('请选择要上架的商品')
          return
        }
        msg.delConfirm(`确定要上架选中的 ${selections.value.length} 个商品吗？`).then(() => {
          batchOnShelf(selections.value.map(Number)).then((res) => {
            res.code === ResultCode.SUCCESS ? msg.success(res.message) : msg.error(res.message)
            proTableRef.value.refresh()
          })
        })
      },
      show: () => hasAuth(['product:shelf']),
    },
    {
      text: '批量下架',
      type: 'primary',
      props: { type: 'warning' },
      click: () => {
        if (selections.value.length === 0) {
          msg.warning('请选择要下架的商品')
          return
        }
        msg.delConfirm(`确定要下架选中的 ${selections.value.length} 个商品吗？`).then(() => {
          batchOffShelf(selections.value.map(Number)).then((res) => {
            res.code === ResultCode.SUCCESS ? msg.success(res.message) : msg.error(res.message)
            proTableRef.value.refresh()
          })
        })
      },
      show: () => hasAuth(['product:shelf']),
    },
    {
      text: '批量删除',
      type: 'primary',
      props: { type: 'danger' },
      click: () => {
        if (selections.value.length === 0) {
          msg.warning('请选择要删除的数据')
          return
        }
        msg.delConfirm(`确定删除选中的 ${selections.value.length} 条数据吗？`).then(() => {
          deleteByIds(selections.value.map(Number)).then((res) => {
            res.code === ResultCode.SUCCESS ? msg.success(t('crud.deleteSuccess')) : msg.error(res.message)
            proTableRef.value.refresh()
          })
        })
      },
      show: () => hasAuth(['product:delete']),
    },
  ],
  contextMenu: {
    enabled: true,
    items: [
      {
        text: '上架',
        icon: 'material-symbols:publish',
        show: (record: any) => record.status !== 2 && hasAuth(['product:shelf']),
        handle: (record: any) => {
          msg.delConfirm('确定要上架该商品吗？').then(() => {
            onShelf(record.id).then((res) => {
              res.code === ResultCode.SUCCESS ? msg.success(res.message) : msg.error(res.message)
              proTableRef.value.refresh()
            })
          })
        },
      },
      {
        text: '下架',
        icon: 'material-symbols:unpublished-outline',
        show: (record: any) => record.status === 2 && hasAuth(['product:shelf']),
        handle: (record: any) => {
          msg.delConfirm('确定要下架该商品吗？').then(() => {
            offShelf(record.id).then((res) => {
              res.code === ResultCode.SUCCESS ? msg.success(res.message) : msg.error(res.message)
              proTableRef.value.refresh()
            })
          })
        },
      },
      {
        text: '调整库存',
        icon: 'material-symbols:inventory',
        show: () => hasAuth(['product:stock']),
        handle: (record: any) => {
          maDialog.open({ title: '调整库存', formType: 'stock', data: record })
        },
      },
      {
        text: '编辑',
        icon: 'material-symbols:edit-outline',
        show: () => hasAuth(['product:update']),
        handle: (record: any) => {
          maDialog.open({ title: '编辑商品', formType: 'edit', data: record })
        },
      },
      {
        text: '删除',
        icon: 'material-symbols:delete-outline',
        show: () => hasAuth(['product:delete']),
        handle: (record: any) => {
          msg.delConfirm().then(() => {
            deleteByIds([record.id]).then((res) => {
              res.code === ResultCode.SUCCESS ? msg.success(t('crud.deleteSuccess')) : msg.error(res.message)
              proTableRef.value.refresh()
            })
          })
        },
      },
    ],
  },
}

const columns: MaProTableSchema[] = getTableColumns()
const searchItems: MaProTableSchema[] = getSearchItems()
</script>

<template>
  <div class="p-4">
    <MaProTable
      ref="proTableRef"
      v-model:options="options"
      :columns="columns"
      :search-items="searchItems"
    />

    <MaFormModal v-model:visible="maDialog.dialogVisible.value" v-bind="maDialog.getDialogOptions()">
      <ProductForm
        v-if="['add', 'edit'].includes(maDialog.getFormType())"
        ref="formRef"
        :form-type="maDialog.getFormType()"
        :data="maDialog.getFormData()"
      />
      <StockAdjustForm
        v-if="maDialog.getFormType() === 'stock'"
        ref="stockRef"
        :data="maDialog.getFormData()"
      />
    </MaFormModal>
  </div>
</template>

