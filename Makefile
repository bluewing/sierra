# Dsiable the default command
default:
	@echo "Please choose one of: dcu, dcd, dcexec, clear_logs."

dcu:
	docker compose up -d

dcd:
	docker compose down

dcx:
	docker exec -it bluewing sh