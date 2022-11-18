
docker build -t hc_core_local_image -f ./docker/dependencies.Dockerfile .
docker create --name hc_core_local hc_core_local_image
docker cp hc_core_local:/srv/vendor ./vendor
docker rm hc_core_local
docker rmi hc_core_local_image
