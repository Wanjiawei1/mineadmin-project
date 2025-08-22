#!/bin/bash

FRONTEND_SERVER="wjwfrontend@192.168.10.118"
BACKEND_SERVER="root@192.168.10.119"
FRONTEND_PATH="/opt/mineadmin-frontend/source/web"
BACKEND_PATH="/opt/mineadmin-backend"

case "$1" in
    "push-frontend")
        echo "🚀 部署前端到服务器..."
        rsync -avz --exclude 'node_modules' --exclude 'dist' \
            ./frontend/ $FRONTEND_SERVER:$FRONTEND_PATH/
        
        echo "📦 构建并部署前端..."
        ssh $FRONTEND_SERVER "cd $FRONTEND_PATH && pnpm build && sudo cp -r dist/* /var/www/html/"
        echo "✅ 前端部署完成！访问: http://192.168.10.118"
        ;;
        
    "push-backend")
        echo "🚀 部署后端到服务器..."
        rsync -avz --exclude 'vendor' --exclude 'runtime' \
            ./backend/ $BACKEND_SERVER:$BACKEND_PATH/
        
        echo "🔄 重启后端服务..."
        ssh $BACKEND_SERVER "cd $BACKEND_PATH && sudo -u wjw pkill -f MineAdmin && sleep 2 && sudo -u wjw nohup php bin/hyperf.php start > /dev/null 2>&1 &"
        echo "✅ 后端部署完成！API: http://192.168.10.119:9501"
        ;;
        
    "pull-all")
        echo "📥 从服务器拉取最新代码..."
        rsync -avz --exclude 'node_modules' --exclude 'dist' \
            $FRONTEND_SERVER:$FRONTEND_PATH/ ./frontend/
        rsync -avz --exclude 'vendor' --exclude 'runtime' \
            $BACKEND_SERVER:$BACKEND_PATH/ ./backend/
        echo "✅ 代码同步完成！"
        ;;
        
    "dev-frontend")
        echo "🛠️ 启动前端开发服务器..."
        cd frontend
        if [ ! -d "node_modules" ]; then
            echo "📦 安装前端依赖..."
            npm install
        fi
        
        cat > .env.development << 'ENVEOF'
VITE_APP_API_BASEURL=http://192.168.10.119:9501
VITE_OPEN_PROXY=true
VITE_APP_TITLE=MineAdmin-本地开发
VITE_OPEN_vCONSOLE=true
VITE_BUILD_SOURCEMAP=true
ENVEOF
        
        npm run dev
        ;;
        
    *)
        echo "🎯 MineAdmin 开发部署工具"
        echo ""
        echo "使用方法: ./deploy.sh [命令]"
        echo ""
        echo "📋 可用命令:"
        echo "  push-frontend   - 部署前端到生产服务器"
        echo "  push-backend    - 部署后端到生产服务器"
        echo "  pull-all        - 从服务器拉取最新代码"
        echo "  dev-frontend    - 启动前端本地开发"
        echo ""
        echo "🌐 访问地址:"
        echo "  生产前端: http://192.168.10.118"
        echo "  开发前端: http://localhost:5173"
        echo "  后端API:  http://192.168.10.119:9501"
        ;;
esac
