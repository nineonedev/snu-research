up:
	docker-compose up -d web
	make npm-i
	make npm-dev
stop:
	docker-compose stop web
build:
	docker-compose up -d --build web
down:
	docker-compose down 
reset:
	docker-compose down -v --remove-orphans
npm-i:
	docker-compose exec -it web bash -c "npm install"
npm-dev:
	docker-compose exec -it web bash -c "npm run dev"
cache:
	make stop
	docker-compose rm -f web 
	make build