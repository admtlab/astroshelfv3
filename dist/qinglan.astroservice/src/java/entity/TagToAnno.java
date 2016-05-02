/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "tag_to_anno")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "TagToAnno.findAll", query = "SELECT t FROM TagToAnno t"),
    @NamedQuery(name = "TagToAnno.findByTagToAnnoId", query = "SELECT t FROM TagToAnno t WHERE t.tagToAnnoId = :tagToAnnoId"),
    @NamedQuery(name = "TagToAnno.findByTagSrc", query = "SELECT t FROM TagToAnno t WHERE t.tagSrc = :tagSrc")})
public class TagToAnno implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "tag_to_anno_id")
    private Long tagToAnnoId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 20)
    @Column(name = "tag_src")
    private String tagSrc;
    @JoinColumn(name = "anno_tar_id", referencedColumnName = "anno_id")
    @ManyToOne(optional = false)
    private Annotation annoTarId;

    public TagToAnno() {
    }

    public TagToAnno(Long tagToAnnoId) {
        this.tagToAnnoId = tagToAnnoId;
    }

    public TagToAnno(Long tagToAnnoId, String tagSrc) {
        this.tagToAnnoId = tagToAnnoId;
        this.tagSrc = tagSrc;
    }

    public Long getTagToAnnoId() {
        return tagToAnnoId;
    }

    public void setTagToAnnoId(Long tagToAnnoId) {
        this.tagToAnnoId = tagToAnnoId;
    }

    public String getTagSrc() {
        return tagSrc;
    }

    public void setTagSrc(String tagSrc) {
        this.tagSrc = tagSrc;
    }

    public Annotation getAnnoTarId() {
        return annoTarId;
    }

    public void setAnnoTarId(Annotation annoTarId) {
        this.annoTarId = annoTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (tagToAnnoId != null ? tagToAnnoId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof TagToAnno)) {
            return false;
        }
        TagToAnno other = (TagToAnno) object;
        if ((this.tagToAnnoId == null && other.tagToAnnoId != null) || (this.tagToAnnoId != null && !this.tagToAnnoId.equals(other.tagToAnnoId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.TagToAnno[ tagToAnnoId=" + tagToAnnoId + " ]";
    }
    
}
