/*
 * Copyright 2014 fuca.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
package cz.muni.fi.fucikm.sportsclub.misc;

/**
 * Enumerate for representing Comment types
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
public enum CommentType {

    WALLPOST("wallpost"),
    ARTICLE("article"),
    FORUM("forum"),
    USER("user"),
    PHOTO("photo"),
    VIDEO("video"),
    PAGE("page");

    private String en;

    CommentType(String en) {
	this.en = en;
    }
}
