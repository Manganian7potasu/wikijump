/*
 * services/job/service.rs
 *
 * DEEPWELL - Wikijump API provider and database manager
 * Copyright (C) 2021 Wikijump Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use super::prelude::*;
use crate::api::ApiServerState;
use crate::services::RevisionService;
use async_std::task;
use crossfire::mpsc;
use sea_orm::TransactionTrait;
use std::sync::Arc;
use void::Void;

lazy_static! {
    static ref QUEUE: (mpsc::TxUnbounded<Job>, mpsc::RxUnbounded<Job>) =
        mpsc::unbounded_future();
}

macro_rules! source {
    () => {
        QUEUE.0
    };
}

macro_rules! sink {
    () => {
        QUEUE.1
    };
}

#[derive(Debug)]
pub struct JobService;

impl JobService {
    #[inline]
    fn queue_job(ctx: &ServiceContext<'_>, job: Job) {
        source!().send(job).expect("Job channel has disconnected");
    }

    pub fn queue_rerender_pages(
        ctx: &ServiceContext<'_>,
        site_and_page_ids: &[(i64, i64)],
    ) {
        for &(site_id, page_id) in site_and_page_ids {
            Self::queue_job(ctx, Job::RerenderPageId { site_id, page_id });
        }
    }
}

#[derive(Debug)]
pub struct JobRunner {
    state: ApiServerState,
}

impl JobRunner {
    pub fn spawn(state: &ApiServerState) {
        let state = Arc::clone(state);
        let runner = JobRunner { state };
        task::spawn(runner.main_loop());
    }

    async fn main_loop(mut self) -> Void {
        loop {
            tide::log::trace!("Waiting for next job on queue...");
            let job = sink!().recv().await.expect("Job channel has disconnected");
            tide::log::debug!("Received new job item: {:?}", job);

            match self.process_job(job).await {
                Ok(()) => tide::log::debug!("Finished processing job"),
                Err(error) => tide::log::warn!("Error processing job: {}", error),
            }
        }
    }

    async fn process_job(&mut self, job: Job) -> Result<()> {
        let txn = self.state.database.begin().await?;
        let ctx = &ServiceContext::from_raw(&self.state, &txn);

        match job {
            Job::RerenderPageId { site_id, page_id } => {
                RevisionService::rerender(ctx, site_id, page_id).await?;
            }
        }

        txn.commit().await?;
        Ok(())
    }
}