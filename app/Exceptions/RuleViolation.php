<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use RuntimeException;

/** A library rule was broken. The message is safe to show to the person using the app. */
class RuleViolation extends RuntimeException
{
    /** Expected in normal use, so there is nothing to log. */
    public function report(): bool
    {
        return true;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->getMessage()], 422);
        }

        return back()
            ->withInput($request->except(['password', 'password_confirmation', 'current_password']))
            ->with('error', $this->getMessage());
    }
}
