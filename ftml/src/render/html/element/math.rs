/*
 * render/html/element/math.rs
 *
 * ftml - Library to parse Wikidot text
 * Copyright (C) 2019-2021 Wikijump Team
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
use latex2mathml::{latex_to_mathml, DisplayStyle};

pub fn render_math_block(
    log: &Logger,
    ctx: &mut HtmlContext,
    name: &str,
    latex_source: &str,
) {
    info!(
        log,
        "Rendering math block";
        "name" => name,
        "latex-source" => latex_source,
    );

    todo!()
}

pub fn render_math_inline(log: &Logger, ctx: &mut HtmlContext, latex_source: &str) {
    info!(
        log,
        "Rendering math inline";
        "latex-source" => latex_source,
    );

    todo!()
}

fn process_latex(
    log: &Logger,
    latex_source: &str,
    display: DisplayStyle,
) -> Result<String, String> {
    match latex_to_mathml(latex_source, display) {
        Ok(mathml) => {
            info!(
                log,
                "Processed LaTeX -> MathML";
                "display" => str!(display),
                "mathml" => mathml,
            );

            todo!()
        }
        Err(error) => {
            warn!(
                log,
                "Error processing LaTeX -> MathML";
                "display" => str!(display),
                "error" => str!(error),
            );

            todo!()
        }
    }
}