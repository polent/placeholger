#!/usr/bin/env node
/**
 * Zero-dependency JavaScript minifier for placeholger build pipeline.
 * Strips comments and redundant whitespace while preserving strings,
 * template literals, and regex literals.
 */
import fs from 'node:fs';

function isIdentChar(ch) {
  return ch ? /[a-zA-Z0-9_$]/.test(ch) : false;
}

function minifyJs(code) {
  const out = [];
  const len = code.length;
  let i = 0;
  let prevNonSpace = '';
  let pendingSpace = false;
  let pendingNewline = false;

  function canStartRegex(prev) {
    if (!prev) return true;
    return '(=,[!&|?:;{}~^+-*%<>'.includes(prev);
  }

  while (i < len) {
    const ch = code[i];
    const next = code[i + 1] || '';

    // Line comment
    if (ch === '/' && next === '/') {
      i += 2;
      while (i < len && code[i] !== '\n') i++;
      pendingNewline = true;
      continue;
    }

    // Block comment
    if (ch === '/' && next === '*') {
      i += 2;
      while (i < len && !(code[i] === '*' && code[i + 1] === '/')) i++;
      i += 2;
      pendingSpace = true;
      continue;
    }

    // String literal (' or ") or template literal (`)
    if (ch === '\'' || ch === '"' || ch === '`') {
      if (pendingNewline && out.length > 0 && !';{}:,'.includes(prevNonSpace)) {
        out.push('\n');
      } else if (pendingSpace && isIdentChar(prevNonSpace)) {
        out.push(' ');
      }
      pendingSpace = false;
      pendingNewline = false;

      const quote = ch;
      out.push(quote);
      i++;
      while (i < len) {
        const c = code[i];
        out.push(c);
        if (c === '\\') {
          i++;
          if (i < len) out.push(code[i]);
        } else if (c === quote) {
          i++;
          break;
        }
        i++;
      }
      prevNonSpace = quote;
      continue;
    }

    // Regex literal
    if (ch === '/' && canStartRegex(prevNonSpace)) {
      if (pendingSpace && isIdentChar(prevNonSpace)) {
        out.push(' ');
      }
      pendingSpace = false;
      pendingNewline = false;

      out.push('/');
      i++;
      let inClass = false;
      while (i < len) {
        const c = code[i];
        out.push(c);
        if (c === '\\') {
          i++;
          if (i < len) out.push(code[i]);
        } else if (c === '[') {
          inClass = true;
        } else if (c === ']') {
          inClass = false;
        } else if (c === '/' && !inClass) {
          i++;
          while (i < len && /[a-zA-Z]/.test(code[i])) {
            out.push(code[i]);
            i++;
          }
          break;
        }
        i++;
      }
      prevNonSpace = '/';
      continue;
    }

    // Whitespace
    if (ch === ' ' || ch === '\t' || ch === '\r' || ch === '\n') {
      if (ch === '\n') pendingNewline = true;
      pendingSpace = true;
      i++;
      continue;
    }

    // Emit separator if needed
    if (out.length > 0) {
      if (pendingNewline && !';{}([,:'.includes(prevNonSpace) && !';})],'.includes(ch)) {
        // Keep newline for ASI safety when needed, or space if both are ident chars
        if (isIdentChar(prevNonSpace) && isIdentChar(ch)) {
          out.push('\n');
        } else if (prevNonSpace === ')' || prevNonSpace === ']' || prevNonSpace === '\'' || prevNonSpace === '"') {
          if (isIdentChar(ch)) out.push('\n');
        }
      } else if (pendingSpace) {
        if (
          (isIdentChar(prevNonSpace) && isIdentChar(ch)) ||
          (prevNonSpace === '+' && ch === '+') ||
          (prevNonSpace === '-' && ch === '-') ||
          (prevNonSpace === '/' && ch === '/')
        ) {
          out.push(' ');
        }
      }
    }

    pendingSpace = false;
    pendingNewline = false;
    out.push(ch);
    prevNonSpace = ch;
    i++;
  }

  return out.join('').trim() + '\n';
}

const [, , srcPath, destPath] = process.argv;
if (!srcPath || !destPath) {
  console.error('Usage: node scripts/minify-js.mjs <src> <dest>');
  process.exit(1);
}

const input = fs.readFileSync(srcPath, 'utf8');
const output = minifyJs(input);
fs.writeFileSync(destPath, output);
const saved = Buffer.byteLength(input) - Buffer.byteLength(output);
console.log(`  ✓ Minified ${srcPath} -> ${destPath} (${Buffer.byteLength(output)} bytes, saved ${saved} bytes)`);
