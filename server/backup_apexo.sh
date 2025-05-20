#!/bin/bash
# PostgreSQL database backup script for Apexo
# Usage: bash backup_apexo.sh

export PGPASSWORD="your-db-password-here"
pg_dump -U postgres -d apexo > ~/apexo_backup_$(date +%Y%m%d_%H%M%S).sql
