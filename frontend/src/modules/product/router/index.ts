import { RouteRecordRaw } from 'vue-router';
import { Layout } from '@/layouts';

const routes: RouteRecordRaw[] = [
  {
    path: '/product',
    name: 'product',
    component: Layout,
    meta: {
      title: '商品中心',
      i18n: 'product.center',
    },
    children: [
      {
        path: 'manage',
        name: 'product:manage',
        component: () => import('@/modules/product/views/manage/index.vue'),
        meta: {
          title: '商品管理',
          i1n: 'product.manage',
        },
      },
    ],
  },
];

export default routes;
