package de.ts.doccreator.model;

import java.util.ArrayList;

public class DocModel {
	
	private ArrayList<DocFile> files;
	
	public DocModel(){
		files = new ArrayList<DocFile>();
	}
	
	public void addFile(DocFile file){
		files.add(file);
	}
	
	public ArrayList<DocFile> getDocFiles(){
		return files;
	}
	
}
